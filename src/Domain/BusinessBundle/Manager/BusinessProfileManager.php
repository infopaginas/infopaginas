<?php

namespace Domain\BusinessBundle\Manager;

use AntiMattr\GoogleBundle\Analytics;
use AntiMattr\GoogleBundle\Analytics\Impression;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Domain\BusinessBundle\Admin\BusinessProfileAdmin;
use Domain\BusinessBundle\DBAL\Types\UrlType;
use Domain\BusinessBundle\Entity\Area;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\BusinessProfileExtraSearch;
use Domain\BusinessBundle\Entity\BusinessProfilePhone;
use Domain\BusinessBundle\Entity\BusinessProfileWorkingHour;
use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Entity\ChangeSet;
use Domain\BusinessBundle\Entity\ChangeSetEntry;
use Domain\BusinessBundle\Entity\Locality;
use Domain\BusinessBundle\Entity\Media\BusinessGallery;
use Domain\BusinessBundle\Entity\Neighborhood;
use Domain\BusinessBundle\Entity\Review\BusinessReview;
use Domain\BusinessBundle\Entity\SubscriptionPlan;
use Domain\BusinessBundle\Entity\Translation\BusinessProfileTranslation;
use Domain\BusinessBundle\Form\Type\BusinessProfileFormType;
use Domain\BusinessBundle\Model\DayOfWeekModel;
use Domain\BusinessBundle\Model\StatusInterface;
use Domain\BusinessBundle\Model\SubscriptionPlanInterface;
use Domain\BusinessBundle\Repository\BusinessGalleryRepository;
use Domain\BusinessBundle\Repository\BusinessProfileRepository;
use Domain\BusinessBundle\Repository\BusinessReviewRepository;
use Domain\BusinessBundle\Util\ChangeSetCalculator;
use Domain\BusinessBundle\Util\JsonUtil;
use Domain\BusinessBundle\Util\Task\PhoneChangeSetUtil;
use Domain\BusinessBundle\Util\SlugUtil;
use Domain\BusinessBundle\Util\Task\RelationChangeSetUtil;
use Domain\BusinessBundle\Util\Task\TranslationChangeSetUtil;
use Domain\BusinessBundle\Util\Task\WorkingHoursChangeSetUtil;
use Domain\BusinessBundle\VO\Url;
use Domain\EmergencyBundle\Entity\EmergencyBusiness;
use Domain\EmergencyBundle\Manager\EmergencyManager;
use Domain\PageBundle\Entity\Page;
use Domain\ReportBundle\Manager\BaseReportManager;
use Domain\ReportBundle\Model\ExporterInterface;
use Domain\ReportBundle\Util\DatesUtil;
use Domain\SearchBundle\Model\DataType\EmergencySearchDTO;
use Domain\SearchBundle\Model\Manager\SearchManager;
use Domain\SearchBundle\Util\SearchDataUtil;
use Domain\SiteBundle\Utils\Helpers\LocaleHelper;
use FOS\UserBundle\Model\UserInterface;
use Gedmo\Translatable\TranslatableListener;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Oxa\ElasticSearchBundle\Manager\ElasticSearchManager;
use Oxa\GeolocationBundle\Utils\GeolocationUtils;
use Oxa\ManagerArchitectureBundle\Model\Manager\Manager;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;
use Oxa\Sonata\MediaBundle\Entity\Media;
use Oxa\Sonata\MediaBundle\Model\OxaMediaInterface;
use Oxa\Sonata\UserBundle\Entity\User;
use Oxa\VideoBundle\Entity\VideoMedia;
use Oxa\VideoBundle\Manager\VideoManager;
use Domain\BusinessBundle\Entity\Address\Country;
use Sonata\MediaBundle\Entity\MediaManager;
use Sonata\TranslationBundle\Model\Gedmo\AbstractPersonalTranslation;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Oxa\GeolocationBundle\Model\Geolocation\LocationValueObject;
use Domain\SearchBundle\Model\DataType\SearchDTO;
use Domain\SearchBundle\Model\DataType\DCDataDTO;

/**
 * Class BusinessProfileManager
 * @package Domain\BusinessBundle\Manager
 *
 * @method BusinessProfileRepository getRepository()
 */
class BusinessProfileManager extends Manager
{
    const DEFAULT_LOCALE_NAME = 'San Juan';
    const AUTO_COMPLETE_TYPE  = 'business';
    const AUTO_SUGGEST_MAX_BUSINESSES_COUNT = 5;

    /**
     * @var CategoryManager
     */
    protected $categoryManager;

    /** @var LocalityManager */
    protected $localityManager;

    /** @var EmergencyManager */
    protected $emergencyManager;

    /** @var  TranslatableListener */
    private $translatableListener;

    /** @var FormFactory */
    private $formFactory;

    /** @var Analytics $analytics */
    private $analytics;

    /** @var  MediaManager */
    private $sonataMediaManager;

    /** @var ElasticSearchManager $elasticSearchManager */
    private $elasticSearchManager;

    /** @var ContainerInterface $container */
    private $container;

    /** @var int $position */
    private $position = 1;

    /**
     * Manager constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->em = $container->get('doctrine.orm.entity_manager');

        $this->categoryManager = $container->get('domain_business.manager.category');
        $this->localityManager = $container->get('domain_business.manager.locality');
        $this->emergencyManager = $container->get('domain_emergency.manager.emergency');

        $this->translatableListener = $container->get('sonata_translation.listener.translatable');

        $this->formFactory = $container->get('form.factory');

        $this->sonataMediaManager = $container->get('sonata.media.manager.media');

        $this->analytics = $container->get('google.analytics');

        $this->elasticSearchManager = $container->get('oxa_elastic_search.manager.search');
    }

    /**
     * @param string $query
     * @param string $locale
     *
     * @return array
     */
    public function searchCategoryAndBusinessAutosuggestByPhrase($query, $locale)
    {
        $categories = $this->searchCategoryAutoSuggestInElastic(
            $query,
            $locale,
            CategoryManager::AUTO_SUGGEST_MAX_CATEGORY_MAIN_COUNT
        );
        $businessProfiles = $this->searchBusinessAutoSuggestInElastic(
            $query,
            $locale,
            self::AUTO_SUGGEST_MAX_BUSINESSES_COUNT
        );

        $result = array_merge($businessProfiles, $categories);

        return $result;
    }

    /**
     * @param string   $query
     * @param string   $locale
     * @param int|null $limit
     *
     * @return array
     */
    public function searchCategoryAutosuggestByPhrase($query, $locale, $limit = null)
    {
        $categories = $this->searchCategoryAutoSuggestInElastic($query, $locale, $limit);

        return $categories;
    }

    /**
     * @param BusinessProfile[] $profilesList
     *
     * @return string
     */
    public function getLocationMarkersFromProfileData(array $profilesList)
    {
        $profilesArray = [];

        foreach ($profilesList as $profile) {
            $logoPath = null;
            $logo = $profile->getLogo();

            if ($logo) {
                $provider = $this->container->get($logo->getProviderName());

                $logoPath = $provider->generatePublicUrl($logo, 'admin');
            }

            $backgndPath = null;
            $backgnd = $profile->getBackground();

            if ($backgnd) {
                $provider = $this->container->get($backgnd->getProviderName());

                $backgndPath = $provider->generatePublicUrl($backgnd, 'admin');
            }

            $profilesArray[] = [
                'id'            => $profile->getId(),
                'name'          => $profile->getName(),
                'address'       => $profile->getShortAddress(),
                'logo'          => $logoPath,
                'background'    => $backgndPath,
                'latitude'      => $profile->getLatitude(),
                'longitude'     => $profile->getLongitude(),
                'labelNumber'   => (string) $profile->getDisplayedPosition(),
                'profileUrl'    => $this->container->get('router')->generate(
                    'domain_business_profile_view',
                    [
                        'slug'      => $profile->getSlug(),
                        'citySlug'  => $profile->getCitySlug(),
                    ]
                ),
            ];
        }

        if (!$profilesArray) {
            $profilesArray[] = $this->getDefaultLocationMarkers(false);
        }

        return json_encode($profilesArray);
    }

    /**
     * @param Locality[] $localities
     *
     * @return string
     */
    public function getLocationMarkersFromLocalityData($localities)
    {
        $data = [];

        foreach ($localities as $locality) {
            if ($locality->getLatitude() and $locality->getLongitude()) {
                $data[] = [
                    'id'        => $locality->getId(),
                    'name'      => $locality->getName(),
                    'latitude'  => $locality->getLatitude(),
                    'longitude' => $locality->getLongitude(),
                ];
            }
        }

        if (!$data) {
            $data[] = $this->getDefaultLocationMarkers(false);
        }

        return json_encode($data);
    }

    /**
     * @param bool $isEncoded
     *
     * @return array|string
     */
    public function getDefaultLocationMarkers($isEncoded = true)
    {
        $defaultCenterCoordinates = $this->container->getParameter('map_default_center');
        $defaultCenterName        = $this->container->getParameter('map_default_center_name');
        $coordinates = explode(',', $defaultCenterCoordinates);

        $profilesArray = [
            'id'            => 0,
            'name'          => $defaultCenterName,
            'latitude'      => $coordinates[0],
            'longitude'     => $coordinates[1],
        ];

        if ($isEncoded) {
            $data = json_encode([$profilesArray]);
        } else {
            $data = $profilesArray;
        }

        return $data;
    }

    /**
     * @param SearchDTO $searchParams
     *
     * @return array
     */
    public function search(SearchDTO $searchParams)
    {
        return $this->searchBusinessInElastic($searchParams);
    }

    /**
     * @param SearchDTO $searchParams
     *
     * @return array
     */
    public function searchCatalog(SearchDTO $searchParams)
    {
        $searchResultsData = $this->searchCatalogBusinessInElastic($searchParams);

        return $searchResultsData;
    }

    /**
     * @param SearchDTO $searchParams
     *
     * @return array
     */
    public function searchSuggestedBusinesses(SearchDTO $searchParams)
    {
        return $this->searchSuggestedBusinessesInElastic($searchParams);
    }

    /**
     * @param SearchDTO $searchParams
     *
     * @return array
     */
    public function searchClosestBusinesses(SearchDTO $searchParams)
    {
        $searchResultsData = $this->searchClosestBusinessesInElastic($searchParams);

        return $searchResultsData;
    }

    /**
     * @param EmergencySearchDTO $searchParams
     *
     * @return array
     */
    public function searchEmergencyBusinesses(EmergencySearchDTO $searchParams)
    {
        $searchResultsData = $this->searchEmergencyBusinessesInElastic($searchParams);

        return $searchResultsData;
    }

    /**
     * @param int $id
     * @param string $locale
     *
     * @return null|object
     */
    public function find(int $id, string $locale = LocaleHelper::DEFAULT_LOCALE)
    {
        $business = $this->getRepository()->find($id);

        if ($business) {
            $this->getTranslatableListener()->setTranslatableLocale($locale);
            $this->getTranslatableListener()->setTranslationFallback('');

            $this->getEntityManager()->refresh($business);
        }

        return $business;
    }

    /**
     * @param string $uid
     * @return null|object
     */
    public function findByUid(string $uid)
    {
        $businessProfile = $this->getRepository()->findOneBy([
            'uid' => $uid,
        ]);

        return $businessProfile;
    }

    /**
     * @param string $slug
     * @return null|object
     */
    public function findBySlug(string $slug)
    {
        $customSlug = SlugUtil::convertCustomSlug($slug);

        $businessProfile = $this->getRepository()->findBySlug($slug, $customSlug);

        return $businessProfile;
    }

    /**
     * @param string $slug
     * @return null|object
     */
    public function findByAlias(string $slug)
    {
        $slug = SlugUtil::convertSlug($slug);

        $businessProfile = $this->getRepository()->findByAlias($slug);

        return $businessProfile;
    }

    /**
     * @return BusinessProfile
     */
    public function createProfile() : BusinessProfile
    {
        $country = $this->getDefaultProfileCountry();

        $profile = new BusinessProfile();
        $profile->setIsActive(false);
        $profile->setCountry($country);

        return $profile;
    }

    /**
     * @param BusinessProfile $businessProfile
     */
    public function saveProfile(BusinessProfile $businessProfile)
    {
        $businessProfile->setIsActive(false);

        $this->commit($businessProfile);
    }

    /**
     * @param BusinessProfile $businessProfile
     */
    public function activate(BusinessProfile $businessProfile)
    {
        $businessProfile->setIsActive(true);
        $this->commit($businessProfile);
    }

    /**
     * @param BusinessProfile $businessProfile
     */
    public function deactivate(BusinessProfile $businessProfile)
    {
        $businessProfile->setIsActive(false);
        $businessProfile->setIsClosed(true);
        $this->commit($businessProfile);
    }

    /**
     * @param BusinessProfile $businessProfile
     * @param User            $user
     */
    public function claim(BusinessProfile $businessProfile, User $user)
    {
        $businessProfile->setUser($user);

        $this->commit($businessProfile);
    }

    /**
     * @param BusinessProfile $businessProfile
     */
    public function remove(BusinessProfile $businessProfile)
    {
        $this->drop($businessProfile);
    }

    /**
     * @param BusinessProfile $businessProfile
     * @param ChangeSet $changeSet
     */
    public function publish(BusinessProfile $businessProfile, ChangeSet $changeSet)
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        /** @var ChangeSetEntry $change */
        foreach ($changeSet->getEntries() as $change) {
            switch ($change->getAction()) {
                case ChangeSetCalculator::CHANGE_COMMON_PROPERTY:
                    $newValue = $change->getNewValue();

                    if (in_array($change->getFieldName(), BusinessProfile::getCommonBooleanFields())) {
                        $newValue = (bool)$newValue;
                    }

                    $accessor->setValue($businessProfile, $change->getFieldName(), $newValue);

                    break;
                case ChangeSetCalculator::CHANGE_RELATION_MANY_TO_ONE:
                    $item = RelationChangeSetUtil::getRelationEntityFromChangeSet(
                        $change,
                        $this->getEntityManager()
                    );

                    if ($item) {
                        $accessor->setValue($businessProfile, $change->getFieldName(), $item);
                    }

                    break;
                case ChangeSetCalculator::CHANGE_RELATION_ONE_TO_MANY:
                    switch ($change->getClassName()) {
                        case BusinessProfilePhone::class:
                            $collection = PhoneChangeSetUtil::getPhonesCollectionsFromChangeSet(
                                $change,
                                $businessProfile,
                                $this->getEntityManager()
                            );

                            break;
                        case BusinessProfileWorkingHour::class:
                            $collection = WorkingHoursChangeSetUtil::getWorkingHoursCollectionFromChangeSet(
                                $change,
                                $businessProfile,
                                $this->getEntityManager()
                            );

                            break;
                        default:
                            $collection = null;

                            break;
                    }

                    if ($collection) {
                        $accessor->setValue($businessProfile, $change->getFieldName(), $collection);
                    }

                    break;
                case ChangeSetCalculator::CHANGE_RELATION_MANY_TO_MANY:
                    $ids = array_map(function ($element) {
                        return $element->id;
                    }, json_decode($change->getNewValue()));

                    $collection = $this->getEntitiesByIds($change->getClassName(), $ids);
                    $accessor->setValue($businessProfile, $change->getFieldName(), $collection);

                    break;
                case ChangeSetCalculator::CHANGE_MEDIA_RELATION_MANY_TO_ONE:
                    $dataNew  = json_decode($change->getNewValue());
                    $dataOld  = json_decode($change->getOldValue());

                    if ($dataNew) {
                        // add or update
                        $media = $this->getEntityManager()->getRepository($change->getClassName())->find($dataNew->id);

                        if ($media) {
                            if ($change->getClassName() == VideoMedia::class) {
                                $media->setTitle($dataNew->title);
                                $media->setDescription($dataNew->description);

                                if (!$dataOld) {
                                    // add
                                    if ($media->getYoutubeSupport()) {
                                        $media->setYoutubeAction(VideoMedia::YOUTUBE_ACTION_ADD);
                                    }
                                } elseif ($dataOld->id != $dataNew->id) {
                                    // replace
                                    $this->getVideoManager()->scheduleVideoForRemove($dataOld->id);

                                    if ($media->getYoutubeSupport()) {
                                        $media->setYoutubeAction(VideoMedia::YOUTUBE_ACTION_ADD);
                                    }
                                } else {
                                    // update property
                                    if ($media->getYoutubeSupport() and !$media->getYoutubeAction() and
                                        $media->getYoutubeId()
                                    ) {
                                        $media->setYoutubeAction(VideoMedia::YOUTUBE_ACTION_UPDATE);
                                    }
                                }
                            }

                            $accessor->setValue($businessProfile, $change->getFieldName(), $media);
                        }
                    } else {
                        // remove
                        if ($change->getClassName() == VideoMedia::class and $businessProfile->getVideo()) {
                            $this->getVideoManager()->scheduleVideoForRemove($businessProfile->getVideo()->getId());
                        }

                        $accessor->setValue($businessProfile, $change->getFieldName(), null);
                    }

                    break;
                case ChangeSetCalculator::CHANGE_RELATION_URL_TYPE:
                    $newValue = json_decode($change->getNewValue(), true);

                    if ($newValue) {
                        $value = new Url();

                        if (array_key_exists(UrlType::URL_NAME, $newValue)) {
                            $value->setUrl($newValue[UrlType::URL_NAME]);
                        }

                        if (array_key_exists(UrlType::REL_NO_OPENER, $newValue)) {
                            $value->setRelNoOpener((bool)$newValue[UrlType::REL_NO_OPENER]);
                        }

                        if (array_key_exists(UrlType::REL_NO_FOLLOW, $newValue)) {
                            $value->setRelNoFollow((bool)$newValue[UrlType::REL_NO_FOLLOW]);
                        }

                        if (array_key_exists(UrlType::REL_NO_REFERRER, $newValue)) {
                            $value->setRelNoReferrer((bool)$newValue[UrlType::REL_NO_REFERRER]);
                        }

                        if (array_key_exists(UrlType::REL_SPONSORED, $newValue)) {
                            $value->setRelSponsored((bool)$newValue[UrlType::REL_SPONSORED]);
                        }

                        if (array_key_exists(UrlType::REL_UGC, $newValue)) {
                            $value->setRelUGC((bool)$newValue[UrlType::REL_UGC]);
                        }
                    } else {
                        $value = null;
                    }

                    $accessor->setValue($businessProfile, $change->getFieldName(), $value);

                    break;
                case ChangeSetCalculator::CHANGE_MEDIA_RELATION_ONE_TO_MANY:
                    $dataNew = json_decode($change->getNewValue());
                    $dataOld = json_decode($change->getOldValue());

                    if ($dataOld) {
                        foreach ($dataOld as $key => $itemOld) {
                            if (!empty($dataNew[$key])) {
                                $dataNew[$key]->id = $itemOld->id;
                            }
                        }
                    }

                    $collection = new ArrayCollection();

                    if ($dataNew) {
                        foreach ($dataNew as $item) {
                            $media = $this->em->getRepository(Media::class)->find($item->media);

                            if ($media) {
                                if (!$item->id) {
                                    $gallery = new BusinessGallery();

                                    $this->em->persist($gallery);
                                } else {
                                    $gallery = $this->em->getRepository(BusinessGallery::class)->find($item->id);
                                }

                                $gallery->setMedia($media);
                                $gallery->setDescription($item->description);

                                $collection->add($gallery);
                            }
                        }
                    }

                    if ($collection) {
                        $accessor->setValue($businessProfile, $change->getFieldName(), $collection);
                    }

                    break;
            }
        }

        $this->getEntityManager()->persist($businessProfile);
        $this->getEntityManager()->flush();

        /** @var ChangeSetEntry $change */
        foreach ($changeSet->getEntries() as $change) {
            // workaround to override translation after gedmo translatable callback
            switch ($change->getAction()) {
                case ChangeSetCalculator::CHANGE_TRANSLATION:
                    if ($change->getClassName() === BusinessProfileTranslation::class) {
                        $dataNew = json_decode($change->getNewValue());
                        $dataOld = json_decode($change->getOldValue());

                        if ($dataNew and $dataNew->field and $dataNew->locale and $dataNew->value) {
                            $translation = $businessProfile->getTranslationItem($dataNew->field, $dataNew->locale);

                            if (!$translation) {
                                $translation = new BusinessProfileTranslation();

                                $businessProfile->addTranslation($translation);
                            }

                            $translation->setField($dataNew->field);
                            $translation->setLocale($dataNew->locale);
                            $translation->setContent($dataNew->value);

                        } elseif ($dataOld and $dataOld->field and $dataOld->locale) {
                            $translation = $businessProfile->getTranslationItem($dataOld->field, $dataOld->locale);

                            if ($translation) {
                                $businessProfile->removeTranslation($translation);
                            }
                        }
                    }
                    break;
            }
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @param ChangeSet $changeSet
     */
    public function handleRejectedTaskContent(ChangeSet $changeSet)
    {
        /** @var ChangeSetEntry $change */
        foreach ($changeSet->getEntries() as $change) {
            switch ($change->getAction()) {
                case ChangeSetCalculator::CHANGE_MEDIA_RELATION_MANY_TO_ONE:
                    $dataNew  = json_decode($change->getNewValue());

                    if ($dataNew) {
                        $media = $this->getEntityManager()->getRepository($change->getClassName())->find($dataNew->id);

                        if ($media) {
                            if ($media instanceof VideoMedia and $media->getBusinessProfiles()->isEmpty()) {
                                $media->setIsDeleted(true);
                            } elseif ($media instanceof Media) {
                                switch ($media->getContext()) {
                                    case Media::CONTEXT_BUSINESS_PROFILE_LOGO:
                                        if ($media->getLogoBusinessProfiles()->isEmpty()) {
                                            $media->setIsDeleted(true);
                                        }

                                        break;
                                    case Media::CONTEXT_BUSINESS_PROFILE_BACKGROUND:
                                        if ($media->getBackgroundBusinessProfiles()->isEmpty()) {
                                            $media->setIsDeleted(true);
                                        }

                                        break;
                                }
                            }
                        }
                    }

                    break;
                case ChangeSetCalculator::CHANGE_MEDIA_RELATION_ONE_TO_MANY:
                    $dataNew = json_decode($change->getNewValue());

                    if ($dataNew) {
                        foreach ($dataNew as $item) {
                            $media = $this->em->getRepository(Media::class)->find($item->media);

                            if ($media and $media->getBusinessGallery()->isEmpty()) {
                                $media->setIsDeleted(true);
                            }
                        }
                    }

                    break;
            }
        }
    }

    /**
     * @param ChangeSetEntry $change
     * @param mixed $value
     *
     * @return string
     */
    public function getTaskMediaLink(ChangeSetEntry $change, $value)
    {
        $url = '';

        switch ($change->getClassName()) {
            case Media::class:
                $data  = json_decode($value);
                $media = $this->getEntityManager()->getRepository(Media::class)->find($data->id);

                if ($media) {
                    $url = $this->getMediaPublicUrl($media, 'reference');
                }

                break;
            case BusinessGallery::class:
                $media = $this->getEntityManager()->getRepository(Media::class)->find($value->media);

                if ($media) {
                    $url = $this->getMediaPublicUrl($media, 'reference');
                }

                break;
            case VideoMedia::class:
                $data = json_decode($value);
                $video = $this->getEntityManager()->getRepository(VideoMedia::class)->find($data->id);

                if ($video) {
                    $url = $this->getVideoManager()->getPublicUrl($video);
                }

                break;
        }

        return $url;
    }

    /**
     * @param BusinessProfile $businessProfile
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getBusinessProfileAsForm(BusinessProfile $businessProfile)
    {
        return $this->formFactory->create(new BusinessProfileFormType(), $businessProfile);
    }

    /**
     * @param string $name
     * @param string $data
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getWysiwygPreviewForm($name, $data)
    {
        $form = $this->formFactory->createNamedBuilder($name)
            ->add('description', CKEditorType::class, [
                'label'    => 'Description',
                'required' => false,
                'mapped'   => false,
                'data'     => $data,
                'config_name' => 'preview_text',
                'config'      => [
                    'width'  => '100%',
                    'readOnly' => true,
                ],
                'attr' => [
                    'class' => 'text-editor',
                ],
            ])
            ->getForm();

        return $form;
    }

    /**
     * @param BusinessProfile $businessProfile
     * @param string $locale
     *
     * @return array
     */
    public function getBusinessProfilePhotoImages(BusinessProfile $businessProfile, $locale = LocaleHelper::DEFAULT_LOCALE)
    {
        $subscriptionPlanCode = $businessProfile->getSubscriptionPlanCode();

        if ($subscriptionPlanCode > SubscriptionPlanInterface::CODE_PREMIUM_PLUS) {
            $photos = $this->getBusinessGalleryRepository()->findBusinessProfilePhotoImages($businessProfile, $locale);
            return $photos;
        }

        return [];
    }

    /**
     * @param BusinessProfile $businessProfile
     *
     * @return null|object
     */
    public function getLastReviewForBusinessProfile(BusinessProfile $businessProfile)
    {
        $lastReview = $this->getBusinessProfileReviewsRepository()->findBusinessProfileLastReview($businessProfile);
        return $lastReview;
    }

    /**
     * @param BusinessProfile $businessProfile
     *
     * @return float|int
     */
    public function calculateReviewsAvgRatingForBusinessProfile(BusinessProfile $businessProfile)
    {
        $rating = 0;

        $reviewsAmount = $this->getReviewsCountForBusinessProfile($businessProfile);

        $reviews = $this->getBusinessProfileReviewsRepository()->findReviewsByBusinessProfile($businessProfile);

        if ($reviewsAmount) {
            foreach ($reviews as $review) {
                $rating += (int) $review->getRating();
            }

            return round($rating / $reviewsAmount);
        }

        return 0;
    }

    /**
     * @param BusinessProfile $businessProfile
     */
    public function drop(BusinessProfile $businessProfile)
    {
        $this->getEntityManager()->remove($businessProfile);
        $this->getEntityManager()->flush();
    }

    /**
     * @param SearchDTO $searchParams
     * @param string    $locale
     * @return mixed
     */
    public function countCatalogSearchResults(SearchDTO $searchParams, string $locale)
    {
        return $this->getRepository()->countCatalogSearchResults($searchParams, $locale);
    }

    /**
     * @param BusinessProfile $businessProfile
     * @return int
     */
    public function getReviewsCountForBusinessProfile(BusinessProfile $businessProfile)
    {
        return $this->getBusinessProfileReviewsRepository()->getReviewsCountForBusinessProfile($businessProfile);
    }

    /**
     * @param $searchResultsDTO
     * @return mixed
     */
    public function removeItemWithHiddenAddress($searchResultsDTO)
    {
        foreach ($searchResultsDTO->resultSet as $key => $item) {
            if ($item->getHideAddress()) {
                unset($searchResultsDTO->resultSet[$key]);
            }
        }

        return $searchResultsDTO;
    }

    /**
     * @param BusinessProfile $businessProfile
     *
     * @return bool
     */
    public function isAdUsageReportAllowedForBusiness(BusinessProfile $businessProfile)
    {
        $isAllowed = false;

        $code = $businessProfile->getSubscriptionPlanCode();

        if ($businessProfile->getDcOrderId() and $code >= SubscriptionPlanInterface::CODE_PRIORITY) {
            $isAllowed = true;
        }

        return $isAllowed;
    }

    /**
     * Persist & flush
     *
     * @access private
     * @param BusinessProfile $businessProfile
     */
    private function commit(BusinessProfile $businessProfile)
    {
        $this->getEntityManager()->persist($businessProfile);
        $this->getEntityManager()->flush();
    }

    /**
     * @return MediaManager
     */
    protected function getSonataMediaManager() : MediaManager
    {
        return $this->sonataMediaManager;
    }

    /**
     * @return BusinessGalleryManager
     */
    protected function getBusinessGalleryManager() : BusinessGalleryManager
    {
        return $this->container->get('domain_business.manager.business_gallery');
    }

    /**
     * @return BusinessGalleryRepository
     */
    private function getBusinessGalleryRepository() : BusinessGalleryRepository
    {
        return $this->getEntityManager()->getRepository(BusinessGallery::class);
    }

    private function getGoogleAnalytics()
    {
        return $this->analytics;
    }

    /**
     * @return TranslatableListener
     */
    private function getTranslatableListener() : TranslatableListener
    {
        return $this->translatableListener;
    }

    /**
     * @return BusinessReviewRepository
     */
    private function getBusinessProfileReviewsRepository()
    {
        return $this->getEntityManager()->getRepository(BusinessReview::class);
    }

    /**
     * @return EntityManager
     */
    protected function getEntityManager() : EntityManager
    {
        return $this->em;
    }

    /**
     * @param BusinessProfile $profile
     *
     * @return DCDataDTO
     */
    public function getSlugDcDataDTO(BusinessProfile $profile) : DCDataDTO
    {
        $categoriesSet = [];

        foreach ($profile->getCategories() as $category) {
            $categoriesSet[] = $category->getSlug();
        }

        return new DCDataDTO(
            [],
            '',
            $categoriesSet,
            $profile->getSlug()
        );
    }

    /**
     * @param string $class
     * @param array $ids
     *
     * @return mixed
     */
    private function getEntitiesByIds($class, $ids)
    {
        /** @var EntityRepository $repo */
        $repo = $this->getEntityManager()->getRepository($class);
        $objects = $repo->createQueryBuilder('qb')
            ->where('qb.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()->getResult();

        return $objects;
    }

    /**
     * @param int|null      $businessProfileId
     * @param array         $areas
     * @param string|null   $locale
     *
     * @return array
     */
    public function getAreaLocalities($businessProfileId, $areas, $locale)
    {
        $checkedLocalityIds = [];

        if ($businessProfileId) {
            /* @var BusinessProfile $businessProfile */
            $businessProfile    = $this->getRepository()->find($businessProfileId);
            $checkedLocalityIds = $this->getBusinessProfileLocalityIds($businessProfile);
        }

        $localities = $this->getLocalitiesByAreas($areas, $locale);

        $data = $this->getEntitiesMultiSelectResponseForData($localities, $checkedLocalityIds);

        return $data;
    }

    /**
     * @param int|null      $businessProfileId
     * @param array         $localities
     * @param string|null   $locale
     *
     * @return array
     */
    public function getLocalitiesNeighborhoods($businessProfileId, $localities, $locale)
    {
        $checkedNeighborhoodIds = [];

        if ($businessProfileId) {
            /* @var BusinessProfile $businessProfile */
            $businessProfile    = $this->getRepository()->find($businessProfileId);
            $checkedNeighborhoodIds = $this->getBusinessProfileNeighborhoodIds($businessProfile);
        }

        $neighborhoods = $this->getNeighborhoodsByLocalities($localities, $locale);

        $data = $this->getEntitiesMultiSelectResponseForData($neighborhoods, $checkedNeighborhoodIds);

        return $data;
    }

    /**
     * @param BusinessProfile $businessProfile
     *
     * @return array
     */
    public function getBusinessProfileLocalityIds($businessProfile)
    {
        $localities = $businessProfile->getLocalities();

        $data = $this->getEntitiesId($localities);

        return $data;
    }

    /**
     * @param BusinessProfile $businessProfile
     *
     * @return array
     */
    public function getBusinessProfileNeighborhoodIds($businessProfile)
    {
        $neighborhoods = $businessProfile->getNeighborhoods();

        $data = $this->getEntitiesId($neighborhoods);

        return $data;
    }

    /**
     * @param array  $areas
     * @param string $locale
     *
     * @return Locality[]
     */
    public function getLocalitiesByAreas($areas, $locale)
    {
        $localities = $this->getEntityManager()->getRepository(Locality::class)
            ->getAvailableLocalitiesByAres($areas, $locale);

        return $localities;
    }

    /**
     * @param array  $localities
     * @param string $locale
     *
     * @return Neighborhood[]
     */
    public function getNeighborhoodsByLocalities($localities, $locale)
    {
        $neighborhoods = $this->getEntityManager()->getRepository(Neighborhood::class)
            ->getAvailableNeighborhoodsByLocalities($localities, $locale);

        return $neighborhoods;
    }

    /**
     * @param array $entities
     *
     * @return array
     */
    private function getEntitiesId($entities)
    {
        $data = [];

        foreach ($entities as $item) {
            $data[] = $item->getId();
        }

        return $data;
    }

    /**
     * @param array $entities
     * @param array $checkedIds
     *
     * @return array
     */
    private function getEntitiesMultiSelectResponseForData($entities, $checkedIds)
    {
        $data = [];

        foreach ($entities as $key => $item) {
            $data[$key] = [
                'id'       => $item->getId(),
                'name'     => $item->getName(),
                'selected' => false,
            ];

            if (in_array($item->getId(), $checkedIds)) {
                $data[$key]['selected'] = true;
            }
        }

        return $data;
    }

    /**
     * see https://developers.google.com/search/docs/data-types/local-businesses
     * @param BusinessProfile[]  $businessProfiles
     * @param bool               $showAll
     *
     * @return string
     */
    public function buildBusinessProfilesSchema($businessProfiles, $showAll = false)
    {
        $schema = [];

        foreach ($businessProfiles as $businessProfile) {
            $schemaItem = $this->buildBaseLocalBusinessSchema($businessProfile);

            $email = $businessProfile->getEmail();
            if ($email) {
                $schemaItem['email'] = $email;
            }

            if (!$businessProfile->getHideAddress()) {
                $customAddress = $businessProfile->getCustomAddress();

                if (!$customAddress) {
                    $country = $this->getDefaultProfileCountry();

                    $schemaItem['address'] = [
                        '@type'           => 'PostalAddress',
                        'addressLocality' => $businessProfile->getCity(),
                        'streetAddress'   => $businessProfile->getStreetAddress(),
                        'postalCode'      => $businessProfile->getZipCode(),
                    ];

                    if ($country->getShortName() === strtoupper(Country::PUERTO_RICO_SHORT_NAME)) {
                        $schemaItem['address']['addressCountry'] = strtoupper(Country::USA_SHORT_NAME);
                        $schemaItem['address']['addressRegion']  = $country->getName();
                    } else {
                        $schemaItem['address']['addressCountry'] = $country->getName();
                        $state = $businessProfile->getState();

                        if ($state) {
                            $schemaItem['address']['addressRegion'] = $state;
                        }
                    }
                }

                $schemaItem['hasMap'] = 'https://maps.google.com/?q=' . $businessProfile->getLatLng();

                $schemaItem['geo'] = [
                    '@type'     => 'GeoCoordinates',
                    'latitude'  => $businessProfile->getLatitude(),
                    'longitude' => $businessProfile->getLongitude(),
                ];
            }

            $schemaItem = $this->getBusinessProfileWorkingHoursSchema($businessProfile, $schemaItem);

            $phones = $businessProfile->getPhones();
            foreach ($phones as $phone) {
                $schemaItem['telephone'][] = $phone->getPhone();
            }

            if ($showAll) {
                $description = $businessProfile->getDescription();
                if ($description) {
                    $schemaItem['description'] = strip_tags($description);
                }

                $sameAs = $this->addSameAsUrl([], $businessProfile->getFacebookLink());
                $sameAs = $this->addSameAsUrl($sameAs, $businessProfile->getTwitterLink());
                $sameAs = $this->addSameAsUrl($sameAs, $businessProfile->getGoogleLink());
                $sameAs = $this->addSameAsUrl($sameAs, $businessProfile->getYoutubeLink());
                $sameAs = $this->addSameAsUrl($sameAs, $businessProfile->getInstagramLink());
                $sameAs = $this->addSameAsUrl($sameAs, $businessProfile->getTripAdvisorLink());
                $sameAs = $this->addSameAsUrl($sameAs, $businessProfile->getLinkedInLink());

                $photos = $this->getBusinessProfilePhotoImages($businessProfile);

                foreach ($photos as $photo) {
                    $schemaItem['image'][] = $this->getMediaPublicUrl($photo->getMedia(), 'preview');
                }
            }

            if (empty($schemaItem['image'])) {
                $schemaItem['image'] = $this->getDefaultLocalBusinessImage($schemaItem, $businessProfile);
            }

            if (!empty($sameAs)) {
                if (!empty($schemaItem['sameAs'])) {
                    $sameAs[] = $schemaItem['sameAs'];
                    $schemaItem['sameAs'] = $sameAs;
                }

                $schemaItem['sameAs'] = $sameAs;
            }

            $schema[] = $schemaItem;
        }

        return JsonUtil::jsonHtmlEntitiesEncode(JsonUtil::htmlEntitiesEncode($schema));
    }

    /**
     * see https://developers.google.com/search/docs/data-types/reviews
     * @param BusinessReview[] $reviews
     * @param BusinessProfile $businessProfile
     *
     * @return string
     */
    public function buildBusinessProfileReviewsSchema($reviews, $businessProfile)
    {
        $schemaItem = $this->buildBaseLocalBusinessSchema($businessProfile);

        foreach ($reviews as $review) {
            $schemaItem['review'][] = $this->buildReviewItem($review);
        }

        $schemaItem['image'] = $this->getDefaultLocalBusinessImage($schemaItem, $businessProfile);

        return JsonUtil::jsonHtmlEntitiesEncode(JsonUtil::htmlEntitiesEncode([$schemaItem]));
    }

    /**
     * @param Media  $media
     * @param string $format
     *
     * @return string
     */
    public function getMediaPublicUrl($media, $format)
    {
        $provider = $this->container->get($media->getProviderName());
        $format   = $provider->getFormatName($media, $format);
        $url      = $provider->generatePublicUrl($media, $format);

        return $url;
    }

    /**
     * @return VideoManager
     */
    protected function getVideoManager() : VideoManager
    {
        return $this->container->get('oxa.manager.video');
    }

    /**
     * @param BusinessProfile $businessProfile
     *
     * @return array
     */
    private function buildBaseLocalBusinessSchema($businessProfile)
    {
        $schemaItem = [
            '@context'   => 'http://schema.org',
            '@type'      => 'LocalBusiness',
            'name'       => $businessProfile->getName(),
            'branchCode' => $businessProfile->getUid(),
        ];

        $logo = $businessProfile->getLogo();
        if ($logo) {
            $schemaItem['logo'] = $this->getMediaPublicUrl($logo, 'preview');
        }

        $url = $this->getBusinessProfileUrl($businessProfile);
        if ($businessProfile->getWebsiteLink()) {
            $schemaItem['url']    = $businessProfile->getWebsiteLink();
            $schemaItem['sameAs'] = $url;
        } else {
            $schemaItem['url'] = $url;
        }

        return $schemaItem;
    }

    /**
     * @param BusinessReview $review
     *
     * @return array
     */
    private function buildReviewItem($review)
    {
        $item = [
            '@type' => 'Review',
            'author' => [
                '@type' => 'Person',
                'name'  => $review->getUsername(),
            ],
            'datePublished' => $review->getCreatedAt()->format('c'),
            'description'   => $review->getContent(),
            'reviewRating' => [
                '@type'       => 'Rating',
                'worstRating' => BusinessReview::RATING_MIN_VALUE,
                'bestRating'  => BusinessReview::RATING_MAX_VALUE,
                'ratingValue' => $review->getRating(),
            ],
        ];

        return $item;
    }

    /**
     * @param BusinessProfile $businessProfile
     * @param array $schemaItem
     *
     * @return array
     */
    private function getBusinessProfileWorkingHoursSchema($businessProfile, $schemaItem = [])
    {
        if ($businessProfile->getWorkingHoursJsonAsObject()) {
            $dailyHours = DayOfWeekModel::getBusinessProfileWorkingHoursListView($businessProfile);
            $dayOfWeekSchemaOrgMapping = DayOfWeekModel::getDayOfWeekSchemaOrgMapping();

            foreach ($dailyHours as $key => $workingHourItems) {
                $days = explode(',', $key);
                $dayOfWeek = [];

                foreach ($days as $day) {
                    $dayOfWeek[] = $dayOfWeekSchemaOrgMapping[$day];
                }

                if ($dayOfWeek) {
                    $schemaItemTemplate = [
                        '@type'     => 'OpeningHoursSpecification',
                        'dayOfWeek' => $dayOfWeek,
                    ];

                    if ($workingHourItems) {
                        foreach ($workingHourItems as $item) {
                            if ($item->openAllTime) {
                                // open all time
                                $openTime  = DayOfWeekModel::SCHEMA_ORG_OPEN_ALL_DAY_OPEN_TIME;
                                $closeTime = DayOfWeekModel::SCHEMA_ORG_OPEN_ALL_DAY_CLOSE_TIME;
                            } else {
                                $openTime  = $item->timeStart->format(DayOfWeekModel::SCHEMA_ORG_OPEN_TIME_FORMAT);

                                if ($item->timeEnd == DayOfWeekModel::getDefaultDateTime()) {
                                    $closeTime = DayOfWeekModel::SCHEMA_ORG_OPEN_ALL_DAY_CLOSE_TIME;
                                } else {
                                    $closeTime = $item->timeEnd->format(DayOfWeekModel::SCHEMA_ORG_OPEN_TIME_FORMAT);
                                }
                            }

                            $schemaItemTemplate['opens'] = $openTime;
                            $schemaItemTemplate['closes'] = $closeTime;

                            $schemaItem['openingHoursSpecification'][] = $schemaItemTemplate;
                        }
                    } else {
                        // close all time
                        $openTime  = DayOfWeekModel::SCHEMA_ORG_CLOSE_ALL_DAY_OPEN_TIME;
                        $closeTime = DayOfWeekModel::SCHEMA_ORG_CLOSE_ALL_DAY_CLOSE_TIME;

                        $schemaItemTemplate['opens'] = $openTime;
                        $schemaItemTemplate['closes'] = $closeTime;

                        $schemaItem['openingHoursSpecification'][] = $schemaItemTemplate;
                    }
                }
            }
        }

        return $schemaItem;
    }

    /**
     * @param array           $schemaItem
     * @param BusinessProfile $businessProfile
     *
     * @return string
     */
    private function getDefaultLocalBusinessImage($schemaItem, $businessProfile)
    {
        if (!empty($schemaItem['image'])) {
            $url = $schemaItem['image'];
        } elseif (!empty($schemaItem['logo'])) {
            $url = $schemaItem['logo'];
        } else {
            $photos = $this->getBusinessProfilePhotoImages($businessProfile);

            if ($photos) {
                $photo = current($photos);

                $url = $this->getMediaPublicUrl($photo->getMedia(), 'preview');
            } else {
                $request = $this->container->get('request');
                $image   = $this->container->getParameter('default_image');

                $url = $request->getScheme() . '://' . $request->getHost() . $image['path'] . $image['business_image'];
            }
        }

        return $url;
    }

    /**
     * @param array  $sameAs
     * @param string $url
     *
     * @return string
     */
    private function addSameAsUrl($sameAs, $url)
    {
        if ($url) {
            $sameAs[] = $url;
        }

        return $sameAs;
    }

    /**
     * @param BusinessProfile $businessProfile
     *
     * @return string
     */
    public function getBusinessProfileUrl($businessProfile)
    {
        $url = $this->container->get('router')->generate(
            'domain_business_profile_view',
            [
                'citySlug' => $businessProfile->getCitySlug(),
                'slug'     => $businessProfile->getSlug(),
            ],
            true
        );

        return $url;
    }

    public function getBusinessProfileSearchSeoData($locality = null, $categories = [])
    {
        $seoSettings = $this->container->getParameter('seo_custom_settings');

        $titleMaxLength       = $seoSettings['title_max_length'];
        $titleLocalityMaxLength = $seoSettings['locality_length'];
        $descriptionMaxLength = $seoSettings['description_max_length'];
        $descriptionCategoriesMaxLength = $seoSettings['description_category_max_length'];
        $descriptionCategoriesSeparator = $seoSettings['description_category_separator'];
        $categoriesCut = $seoSettings['description_category_cut'];

        $categoryData = [];

        if (!$locality) {
            $localityText = Locality::ALL_LOCALITY_NAME;
        } else {
            $localityText = $locality;
        }

        $localityText = mb_substr($localityText, 0, $titleLocalityMaxLength);

        if ($categories) {
            $itemsCount = count($categories);
            $categoryMaxLength = floor($descriptionCategoriesMaxLength / $itemsCount);

            foreach ($categories as $category) {
                $categoryOutput = mb_substr($category, 0, $categoryMaxLength);

                if (mb_strlen($category) > $categoryMaxLength) {
                    $categoryOutput .= $categoriesCut;
                }

                $categoryData[] = $categoryOutput;
            }
        }

        $categoryText = implode($descriptionCategoriesSeparator, $categoryData);

        $pageManager = $this->container->get('domain_page.manager.page');
        $pageCode = Page::CODE_SEARCH;

        if ($localityText) {
            $data['[locality]'] = $localityText;
        }

        if ($categoryText) {
            $data['[category]'] = $categoryText;
        }

        $page    = $pageManager->getPageByCode($pageCode);
        $seoData = $pageManager->getPageSeoData($page, $data);
        $seoData['title'] = $pageManager->getPageTitle($page, $data);

        $seoData['seoTitle'] = mb_substr($seoData['seoTitle'], 0, $titleMaxLength);
        $seoData['seoDescription'] = mb_substr($seoData['seoDescription'], 0, $descriptionMaxLength);

        return $seoData;
    }

    /**
     * @param Locality $locality
     * @param string   $category
     *
     * @return array
     */
    public function getBusinessProfileCatalogSeoData($locality = null, $category = '')
    {
        $pageManager = $this->container->get('domain_page.manager.page');

        if ($locality) {
            $data['[locality]'] = $locality->getName();
            if ($category) {
                $data['[category]'] = $category;
                $pageCode = Page::CODE_CATALOG_LOCALITY_CATEGORY;
            } else {
                $pageCode = Page::CODE_CATALOG_LOCALITY;

                $categoryOverviewReportManager = $this->container
                    ->get('domain_report.manager.category_overview_report_manager');
                $popularCategoryIds = $categoryOverviewReportManager->getPopularCategoryData($locality->getId());
                $popularCategories  = $this->categoryManager->getAvailableCategoriesByIds($popularCategoryIds);

                foreach ($popularCategories as $key => $popularCategory) {
                    $placeholderKey = Page::getPopularCategoryKey($key + 1);
                    $data[$placeholderKey] = $popularCategory->getName();
                }
            }
        } else {
            $pageCode = Page::CODE_CATALOG;
            $data = [];
        }

        $page    = $pageManager->getPageByCode($pageCode);
        $seoData = $pageManager->getPageSeoData($page, $data);
        $seoData['title'] = $pageManager->getPageTitle($page, $data);

        return $seoData;
    }

    /**
     * @return Country|null
     */
    public function getDefaultProfileCountry()
    {
        $country = $this->em->getRepository(Country::class)->findOneBy(
            ['shortName' => strtoupper(Country::PUERTO_RICO_SHORT_NAME)]
        );

        return $country;
    }

    /**
     * @param SearchDTO $searchParams
     *
     * @return array
     */
    protected function searchCatalogBusinessInElastic(SearchDTO $searchParams)
    {
        $coordinates = $searchParams->getCurrentCoordinates();
        $randomize   = $searchParams->randomizeAllowed();

        if ($searchParams->checkAdsAllowed()) {
            $searchAdQuery   = $this->getCatalogBusinessSearchQueryAd($searchParams);
            $responseAd      = $this->searchElastic(BusinessProfile::ELASTIC_INDEX_AD, $searchAdQuery);
            $searchAd        = $this->getBusinessAdDataFromElasticResponse($responseAd);
            $searchResultAds = $this->setBusinessDynamicValues($searchAd, $coordinates, true);

            $excludeIds = array_keys($searchAd['data']);
        } else {
            $excludeIds      = [];
            $searchResultAds = [];
        }

        $searchQuery = $this->getCatalogBusinessSearchQuery($searchParams, $excludeIds);

        $response = $this->searchElastic(BusinessProfile::ELASTIC_INDEX, $searchQuery);
        $search = $this->getBusinessDataFromElasticResponse($response, $randomize);

        $search = $this->setBusinessDynamicValues($search, $coordinates);

        if ($searchParams->checkAdsAllowed() and $searchResultAds) {
            $searchResultAds['data'] = array_reverse($searchResultAds['data']);

            foreach ($searchResultAds['data'] as $item) {
                array_unshift($search['data'], $item);
            }
        }

        return $search;
    }

    /**
     * @param $search       array
     * @param $coordinates  array
     * @param $isAd         bool
     *
     * @return array
     */
    protected function setBusinessDynamicValues($search, $coordinates, $isAd = false)
    {
        $search['data'] = array_map(function ($item) use ($coordinates, $isAd) {
            /** @var $item BusinessProfile */
            $distance = GeolocationUtils::getDistanceForPoint(
                $coordinates['lat'],
                $coordinates['lng'],
                $item->getLatitude(),
                $item->getLongitude()
            );

            $item->setDisplayedPosition($this->position);
            $this->position++;

            $item->setIsAd($isAd);

            return $item->setDistance($distance);
        }, $search['data']);

        return $search;
    }

    /**
     * @param array $search
     * @param $latitude  float|null
     * @param $longitude float|null
     *
     * @return array
     */
    protected function setEmergencyBusinessDynamicValues($search, $latitude, $longitude)
    {
        $search['data'] = array_map(function ($item) use ($latitude, $longitude) {
            /** @var $item EmergencyBusiness */
            if ($item->getLatitude() and $item->getLongitude()) {
                $distance = GeolocationUtils::getDistanceForPoint(
                    $latitude,
                    $longitude,
                    $item->getLatitude(),
                    $item->getLongitude()
                );

                $item->setDistance($distance);
            }

            return $item;
        }, $search['data']);

        return $search;
    }

    /**
     * @param $searchParams SearchDTO
     *
     * @return array
     */
    protected function searchSuggestedBusinessesInElastic(SearchDTO $searchParams)
    {
        $coordinates = $searchParams->getCurrentCoordinates();

        if ($searchParams->checkAdsAllowed()) {
            $searchAdQuery = $this->getElasticSearchSuggestedAdQuery($searchParams);
            $responseAd    = $this->searchElastic(BusinessProfile::ELASTIC_INDEX_AD, $searchAdQuery);
            $searchAd = $this->getBusinessAdDataFromElasticResponse($responseAd);
            $resultAds = $this->setBusinessDynamicValues($searchAd, $coordinates, true);

            $excludeIds = array_keys($searchAd['data']);
        } else {
            $excludeIds = [];
            $resultAds = [];
        }

        $searchQuery = $this->getElasticSuggestedQuery($searchParams, $excludeIds);

        $response = $this->searchElastic(BusinessProfile::ELASTIC_INDEX, $searchQuery);

        $search = $this->getBusinessDataFromElasticResponse($response);

        $search = $this->setBusinessDynamicValues($search, $coordinates);

        if ($searchParams->checkAdsAllowed() and $resultAds) {
            $resultAds['data'] = array_reverse($resultAds['data']);

            foreach ($resultAds['data'] as $item) {
                array_unshift($search['data'], $item);
            }
        }

        return $search;
    }

    /**
     * @param $searchParams SearchDTO
     *
     * @return array
     */
    protected function searchBusinessInElastic(SearchDTO $searchParams)
    {
        //randomize feature works only for relevance sorting ("Best match")
        $randomize   = $searchParams->randomizeAllowed();
        $coordinates = $searchParams->getCurrentCoordinates();

        if ($searchParams->checkAdsAllowed()) {
            $searchAdQuery   = $this->getElasticSearchQueryAd($searchParams);
            $responseAd      = $this->searchElastic(BusinessProfile::ELASTIC_INDEX_AD, $searchAdQuery);
            $searchAd        = $this->getBusinessAdDataFromElasticResponse($responseAd);
            $searchResultAds = $this->setBusinessDynamicValues($searchAd, $coordinates, true);

            $excludeIds = array_keys($searchAd['data']);
        } else {
            $excludeIds      = [];
            $searchResultAds = [];
        }

        $searchQuery = $this->getElasticSearchQuery($searchParams, $excludeIds);

        $response = $this->searchElastic(BusinessProfile::ELASTIC_INDEX, $searchQuery);

        $search = $this->getBusinessDataFromElasticResponse($response, $randomize);

        $search = $this->setBusinessDynamicValues($search, $coordinates);

        if ($searchResultAds && $searchParams->checkAdsAllowed()) {
            $searchResultAds['data'] = array_reverse($searchResultAds['data']);

            foreach ($searchResultAds['data'] as $item) {
                array_unshift($search['data'], $item);
            }

            $search['total']['value'] += $searchResultAds['total']['value'];
        }

        return $search;
    }

    /**
     * @param string $query
     * @param string $locale
     * @param bool $limit
     *
     * @return array
     */
    protected function searchBusinessAutoSuggestInElastic($query, $locale, $limit = false)
    {
        $searchQuery = $this->getElasticAutoSuggestSearchQuery($query, $locale, $limit);
        $response = $this->searchElastic(BusinessProfile::ELASTIC_INDEX, $searchQuery);
        $search = $this->getBusinessDataFromElasticResponse($response);

        $search['data'] = array_map(function ($item) {
            return [
                'type' => self::AUTO_COMPLETE_TYPE,
                'name' => $item->getName(),
                'data' => $item->getName(),
            ];
        }, $search['data']);

        return $search['data'];
    }

    /**
     * @param $searchParams SearchDTO
     *
     * @return array
     */
    protected function searchClosestBusinessesInElastic(SearchDTO $searchParams)
    {
        $searchQuery = $this->getElasticSearchClosestBusinessesQuery($searchParams);

        $response = $this->searchElastic(BusinessProfile::ELASTIC_INDEX, $searchQuery);

        $search = $this->getBusinessDataFromElasticResponse($response);

        $coordinates = $searchParams->getCurrentCoordinates();

        $search = $this->setBusinessDynamicValues($search, $coordinates);

        return $search;
    }

    /**
     * @param $searchParams EmergencySearchDTO
     *
     * @return array
     */
    protected function searchEmergencyBusinessesInElastic(EmergencySearchDTO $searchParams)
    {
        $searchQuery = $this->getElasticSearchEmergencyBusinessesQuery($searchParams);
        $response    = $this->searchElastic(EmergencyBusiness::ELASTIC_INDEX, $searchQuery);

        $search = $this->emergencyManager->getEmergencyBusinessesFromElasticResponse($response);

        if ($searchParams->sortingByDistanceAvailable()) {
            $search = $this->setEmergencyBusinessDynamicValues($search, $searchParams->lat, $searchParams->lng);
        }

        return $search;
    }

    /**
     * @param string   $query
     * @param string   $locale
     * @param int|null $limit
     *
     * @return array
     */
    protected function searchCategoryAutoSuggestInElastic($query, $locale, $limit = null)
    {
        $searchQuery = $this->categoryManager->getElasticAutoSuggestSearchQuery($query, $locale, $limit);
        $response = $this->searchElastic(Category::ELASTIC_INDEX, $searchQuery);

        $search = $this->categoryManager->getCategoryFromElasticResponse($response);

        $search['data'] = array_map(function ($item) {
            return [
                'type' => CategoryManager::AUTO_COMPLETE_TYPE,
                'name' => $item->getName(),
                'data' => $item->getName(),
                'id'   => $item->getId(),
            ];
        }, $search['data']);

        return $search['data'];
    }

    /**
     * @param SearchDTO $params
     *
     * @return int
     */
    public function searchClosestLocalityInElastic(SearchDTO $params)
    {
        $closestLocality = '';

        if ($params->locationValue->searchCenterLat and $params->locationValue->searchCenterLng) {
            $searchQuery = $this->localityManager->getElasticClosestSearchQuery($params);
            $response = $this->searchElastic(Locality::ELASTIC_INDEX, $searchQuery);

            $search = $this->localityManager->getLocalityFromElasticResponse($response);

            $closestLocality = current($search['data']);
        }

        return $closestLocality;
    }

    /**
     * @param string $index
     * @param array $searchQuery
     *
     * @return array
     */
    protected function searchElastic(string $index, $searchQuery): array
    {
        try {
            $response = $this->elasticSearchManager->search($index, $searchQuery);
        } catch (\Exception $e) {
            $logger = $this->container->get('monolog.logger.elasticsearch');
            $logger->error($e->getMessage());
            $response = [];
        }

        return $response;
    }

    /**
     * @param array $response
     * @param bool $randomize
     *
     * @return array
     */
    protected function getBusinessDataFromElasticResponse($response, $randomize = false)
    {
        $data  = [];
        $sort  = [];
        $total = 0;

        if (!empty($response['hits']['total'])) {
            $total = $response['hits']['total'];
        }

        if (!empty($response['hits']['hits'])) {
            $result = $response['hits']['hits'];
            $dataIds = [];

            foreach ($result as $item) {
                $dataIds[] = $item['_id'];

                if ($randomize) {
                    $sort[] = [
                        'id'   => $item['_id'],
                        'plan' => $item['sort'][0], // subscription plan is always first at order by statement
                        'rank' => $item['sort'][1], // can be rank or distance but randomize is available only for rank
                    ];
                }
            }

            if ($randomize) {
                $dataIds = $this->shuffleSearchResult($sort);
            }

            $dataRaw = $this->getRepository()->findBusinessProfilesByIdsArray($dataIds);

            foreach ($dataIds as $id) {
                $item = $this->searchBusinessByIdsInArray($dataRaw, $id);

                if ($item) {
                    $data[] = $item;
                }
            }
        }

        return [
            'data' => $data,
            'total' => $total,
        ];
    }

    /**
     * @param $response array
     *
     * @return array
     */
    protected function getBusinessAdDataFromElasticResponse($response)
    {
        $data  = [];
        $total = 0;

        if (!empty($response['hits']['total'])) {
            $total = $response['hits']['total'];
        }

        if (!empty($response['aggregations']['ads']['buckets'])) {
            $result = $response['aggregations']['ads']['buckets'];
            $dataIds = [];

            foreach ($result as $item) {
                $dataIds[] = $item['key'];
            }

            // randomize was made on elastic search size
            $dataRaw = $this->getRepository()->findBusinessProfilesByIdsArray($dataIds);

            foreach ($dataIds as $id) {
                $item = $this->searchBusinessByIdsInArray($dataRaw, $id);

                if ($item) {
                    $data[$id] = $item;
                }
            }
        }

        return [
            'data' => $data,
            'total' => $total,
        ];
    }

    /**
     * @param array $data
     * @param int $id
     *
     * @return mixed
     */
    protected function searchBusinessByIdsInArray($data, $id)
    {
        foreach ($data as $item) {
            if ($item->getId() == $id) {
                return $item;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function handleElasticSearchIndexRefresh(): bool
    {
        $status = false;

        $deleteStatus = $this->elasticSearchManager->deleteAllElasticSearchIndexes();

        if ($deleteStatus) {
            $createStatus = $this->elasticSearchManager->createAllElasticIndexes();

            if ($createStatus) {
                $this->getRepository()->setUpdatedAllBusinessProfiles();
                $this->categoryManager->setUpdatedAllCategories();
                $this->localityManager->setUpdatedAllLocalities();
                $this->emergencyManager->setUpdatedAllEmergencyBusinesses();

                $status = true;
            }
        }

        return $status;
    }

    /**
     * @param string $index
     * @param int $id
     *
     * @return bool
     */
    public function removeItemFromElastic(string $index, $id): bool
    {
        $status = true;

        try {
            $this->elasticSearchManager->deleteItem($index, $id);
        } catch (\Exception $e) {
            $status = false;
            $message = json_decode($e->getMessage());

            if (!empty($message->result) &&
                $message->result === ElasticSearchManager::INDEX_NOT_FOUND_EXCEPTION
            ) {
                $status = true;
            }
        }

        return $status;
    }

    /**
     * @param SearchDTO $params
     * @param array $excludeIds
     *
     * @return array
     */
    protected function getElasticSearchQuery(SearchDTO $params, $excludeIds = [])
    {
        $businessSearchQuery = $this->getElasticBusinessSearchQuery($params);

        $filters    = $this->getElasticCommonFilters($params);
        $sort       = $this->getElasticSortQuery($params);

        $locationQuery  = [];
        $locationFilter = $this->getElasticLocationFilter($params);

        if (!$locationFilter) {
            $locationQuery = $this->getElasticLocationQuery($params);
        }

        $searchQuery = $this->getElasticBaseQuery($params->page, $params->limit);
        $searchQuery = $this->addElasticMainQuery($searchQuery, $businessSearchQuery);
        $searchQuery = $this->addElasticSortQuery($searchQuery, $sort);
        $searchQuery = $this->addElasticLocationQuery($searchQuery, $locationQuery);
        $searchQuery = $this->addElasticFiltersQuery($searchQuery, $filters);
        $searchQuery = $this->addElasticLocationFilterQuery($searchQuery, $locationFilter);
        $searchQuery = $this->addElasticExcludeIdsQuery($searchQuery, $excludeIds);

        return $searchQuery;
    }

    protected function getElasticSuggestedQuery(SearchDTO $params, $excludeIds = [])
    {
        $categoryFilters = $this->getElasticMultipleFilters(
            $params,
            $params->getSuggestedCategories(),
            BusinessProfile::ELASTIC_CATEGORIES_FILED,
            $params->getMinimumCategoriesMatch()
        );
        $localityFilters = $this->getElasticMultipleFilters(
            $params,
            $params->getSuggestedLocalities(),
            BusinessProfile::ELASTIC_LOCALITIES_FILED,
            $params->getMinimumLocalitiesMatch()
        );
        $sort = $this->getElasticSubscriptionSortQuery();

        $locationQuery = $this->getElasticLocationQuery($params, $localityFilters[0]);

        $searchQuery = $this->getElasticBaseQuery($params->page, $params->limit);
        $searchQuery = $this->addElasticSortQuery($searchQuery, $sort);
        $searchQuery = $this->addElasticLocationQuery($searchQuery, $locationQuery);
        $searchQuery = $this->addElasticFiltersQuery($searchQuery, $categoryFilters);
        $searchQuery = $this->addElasticExcludeQuery($searchQuery, [
            'match' => [
                'subscr_rank' => SubscriptionPlanInterface::CODE_FREE,
            ],
        ]);
        $searchQuery = $this->addElasticExcludeIdsQuery($searchQuery, $excludeIds);

        return $searchQuery;
    }

    /**
     * @param SearchDTO $params
     *
     * @return array
     */
    protected function getElasticSearchClosestBusinessesQuery(SearchDTO $params)
    {
        $sort = [];

        $sort = array_merge($sort, $this->getElasticGeoSortQuery($params));
        $sort = array_merge($sort, $this->getElasticScoreSortQuery());

        $closestBusinessQuery = $this->getElasticClosestBusinessSearchQuery($params);

        $searchQuery = $this->getElasticBaseQuery($params->page, $params->limit);
        $searchQuery = $this->addElasticMainQuery($searchQuery, $closestBusinessQuery);
        $searchQuery = $this->addElasticSortQuery($searchQuery, $sort);

        return $searchQuery;
    }

    /**
     * @param EmergencySearchDTO $params
     *
     * @return array
     */
    protected function getElasticSearchEmergencyBusinessesQuery(EmergencySearchDTO $params)
    {
        $sort = $this->getEmergencyCatalogElasticSortQuery($params);

        $filters = $this->getElasticEmergencyCatalogFilters($params);

        $searchQuery = $this->getElasticBaseQuery($params->page, $params->limit);
        $searchQuery = $this->addElasticFiltersQuery($searchQuery, $filters);
        $searchQuery = $this->addElasticSortQuery($searchQuery, $sort);

        return $searchQuery;
    }

    /**
     * @param $params SearchDTO
     *
     * @return array
     */
    protected function getElasticSearchQueryAd(SearchDTO $params)
    {
        $businessSearchQuery = $this->getElasticBusinessSearchQuery($params);

        $filters    = $this->getElasticCommonFilters($params);
        $sort       = $this->getElasticRandomSortQuery();

        $locationQuery  = [];
        $locationFilter = $this->getElasticLocationFilter($params);

        if (!$locationFilter) {
            $locationQuery = $this->getElasticLocationQuery($params);
        }

        $searchQuery = $this->getElasticBaseQuery();
        $searchQuery = $this->addElasticMainQuery($searchQuery, $businessSearchQuery);
        $searchQuery = $this->addElasticSortQuery($searchQuery, $sort);
        $searchQuery = $this->addElasticLocationQuery($searchQuery, $locationQuery);
        $searchQuery = $this->addElasticFiltersQuery($searchQuery, $filters);
        $searchQuery = $this->addElasticLocationFilterQuery($searchQuery, $locationFilter);
        $searchQuery = $this->addElasticAdsRandomAggregationQuery($searchQuery, $params->adsPerPage);

        return $searchQuery;
    }

    /**
     * @param $params SearchDTO
     *
     * @return array
     */
    protected function getElasticSearchSuggestedAdQuery(SearchDTO $params)
    {
        $categoryFilters = $this->getElasticMultipleFilters(
            $params,
            $params->getSuggestedCategories(),
            BusinessProfile::ELASTIC_CATEGORIES_FILED,
            $params->getMinimumCategoriesMatch()
        );
        $localityFilters = $this->getElasticMultipleFilters(
            $params,
            $params->getSuggestedLocalities(),
            BusinessProfile::ELASTIC_LOCALITIES_FILED,
            $params->getMinimumLocalitiesMatch()
        );

        $sort = $this->getElasticRandomSortQuery();

        $locationQuery = $this->getElasticLocationQuery($params, $localityFilters[0]);

        $searchQuery = $this->getElasticBaseQuery();
        $searchQuery = $this->addElasticSortQuery($searchQuery, $sort);
        $searchQuery = $this->addElasticLocationQuery($searchQuery, $locationQuery);
        $searchQuery = $this->addElasticFiltersQuery($searchQuery, $categoryFilters);
        $searchQuery = $this->addElasticAdsRandomAggregationQuery($searchQuery, $params->adsPerPage);

        return $searchQuery;
    }

    /**
     * @param SearchDTO $params
     * @param array     $excludeIds
     *
     * @return array
     */
    protected function getCatalogBusinessSearchQuery(SearchDTO $params, $excludeIds = [])
    {
        $sort       = $this->getElasticSortQuery($params);
        $filters    = $this->getElasticCommonFilters($params);
        $filters    = $this->getElasticBusinessCatalogFilters($params, $filters);

        $locationQuery  = [];
        $locationFilter = $this->getElasticLocationFilter($params);

        if (!$locationFilter) {
            $locationQuery = $this->getElasticLocationQuery($params);
        }

        $searchQuery = $this->getElasticBaseQuery($params->page, $params->limit);
        $searchQuery = $this->addElasticSortQuery($searchQuery, $sort);
        $searchQuery = $this->addElasticLocationQuery($searchQuery, $locationQuery);
        $searchQuery = $this->addElasticFiltersQuery($searchQuery, $filters);
        $searchQuery = $this->addElasticLocationFilterQuery($searchQuery, $locationFilter);
        $searchQuery = $this->addElasticExcludeIdsQuery($searchQuery, $excludeIds);

        return $searchQuery;
    }

    /**
     * @param SearchDTO $params
     *
     * @return array
     */
    protected function getCatalogBusinessSearchQueryAd(SearchDTO $params)
    {
        $sort       = $this->getElasticRandomSortQuery();
        $filters    = $this->getElasticCommonFilters($params);
        $filters    = $this->getElasticBusinessCatalogFilters($params, $filters);

        $locationQuery  = [];
        $locationFilter = $this->getElasticLocationFilter($params);

        if (!$locationFilter) {
            $locationQuery = $this->getElasticLocationQuery($params);
        }

        $searchQuery = $this->getElasticBaseQuery();
        $searchQuery = $this->addElasticSortQuery($searchQuery, $sort);
        $searchQuery = $this->addElasticLocationQuery($searchQuery, $locationQuery);
        $searchQuery = $this->addElasticFiltersQuery($searchQuery, $filters);
        $searchQuery = $this->addElasticLocationFilterQuery($searchQuery, $locationFilter);
        $searchQuery = $this->addElasticAdsRandomAggregationQuery($searchQuery, $params->adsPerPage);

        return $searchQuery;
    }

    /**
     * @param SearchDTO $params
     * @param array|null $localityQuery
     * @return array
     */
    protected function getElasticLocationQuery(SearchDTO $params, $localityQuery = null)
    {
        $locationQuery = [];

        if (!$params->locationValue->ignoreLocality) {
            $distanceScript = 'doc["location"].arcDistance(params.lat, params.lng) * params.milesInMeter' .
                ' < doc["miles_of_my_business"].value';

            if ($params->locationValue->locality) {
                $localityId = $params->locationValue->locality->getId();
            } else {
                $localityId = 0;
            }

            $serviceAreasTypeLocality = BusinessProfile::SERVICE_AREAS_LOCALITY_CHOICE_VALUE;

            $locationQuery = [
                'bool' => [
                    'minimum_should_match' => 1,
                    'should' => [
                        [
                            'bool' => [
                                'must' => [
                                    [
                                        'script' => [
                                            'script' => [
                                                'inline' => $distanceScript,
                                                'params' => [
                                                    'lat' => $params->locationValue->lat,
                                                    'lng' => $params->locationValue->lng,
                                                    'milesInMeter' => ElasticSearchManager::MILES_IN_METER,
                                                ]
                                            ]
                                        ],
                                    ],
                                    [
                                        'term' => [
                                            'service_areas_type' => BusinessProfile::SERVICE_AREAS_AREA_CHOICE_VALUE,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'bool' => [
                                'must' => [
                                    $this->getElasticLocalityFilterQuery($localityId, $localityQuery),
                                    [
                                        'term' => [
                                            'service_areas_type' => $serviceAreasTypeLocality,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ];
        }

        return $locationQuery;
    }

    /**
     * @param integer $localityId
     * @param array|null $localityQuery
     * @return array
     */
    private function getElasticLocalityFilterQuery($localityId, $localityQuery = null)
    {
        if (!$localityQuery) {
            return [
                'match' => [
                    'locality_ids' => $localityId,
                ]
            ];
        }

        return $localityQuery;
    }

    /**
     * @param SearchDTO $params
     *
     * @return array
     */
    protected function getElasticLocationFilter(SearchDTO $params)
    {
        $locationFilter = [];

        if ($params->checkSearchInMap()) {
            $locationFilter = [
                'geo_bounding_box' => [
                    'location' => [
                        'top_left' => [
                            'lat' => $params->locationValue->searchBoxTopLeftLat,
                            'lon' => $params->locationValue->searchBoxTopLeftLng,
                        ],
                        'bottom_right' => [
                            'lat' => $params->locationValue->searchBoxBottomRightLat,
                            'lon' => $params->locationValue->searchBoxBottomRightLng,
                        ],
                    ],
                ],
            ];
        }

        return $locationFilter;
    }

    /**
     * @param SearchDTO $params
     *
     * @return array
     */
    protected function getElasticBusinessSearchQuery(SearchDTO $params)
    {
        $fields = $this->getBusinessSearchFields($params->getLocale());

        $query = [
            'bool' => [
                'must' => [
                    [
                        'bool' => [
                            'minimum_should_match' => 1,
                            'should' => [
                                [
                                    'query_string' => [
                                        'default_operator' => 'AND',
                                        'fields' => $fields,
                                        'query'  => $params->query,
                                        'fuzziness' => 'auto',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return $query;
    }

    protected function getElasticSimilarBusinessSearchQuery(string $name, int $id, string $city = ''): array
    {
        $query = [
            'bool' => [
                'must' => [
                    [
                        'query_string' => [
                            'default_operator' => 'AND',
                            'type' => 'most_fields',
                            'fields' => [
                                'name',
                                'name.folded',
                            ],
                            'query' => SearchDataUtil::sanitizeElasticSearchQueryString($name),
                        ],
                    ],
                ],
                'must_not' => [
                    'match' => [
                        '_id' => $id,
                    ],
                ],
            ],
        ];

        if ($city) {
            $query['bool']['must'][] = [
                'query_string' => [
                    'default_operator' => 'AND',
                    'type' => 'most_fields',
                    'fields' => [
                        'city',
                        'city.folded',
                    ],
                    'query' => SearchDataUtil::sanitizeElasticSearchQueryString($city),
                ],
            ];
        }

        return $query;
    }

    /**
     * @param SearchDTO $params
     * @param array     $filters
     *
     * @return array
     */
    protected function getElasticBusinessCatalogFilters(SearchDTO $params, $filters)
    {
        $category = $params->getCategory()->getId();

        if ($category) {
            $filters[] = [
                'match' => [
                    'categories_ids' => $category
                ],
            ];
        }

        return $filters;
    }

    /**
     * @param SearchDTO $params
     *
     * @return array
     */
    protected function getElasticClosestBusinessSearchQuery(SearchDTO $params)
    {
        if ($params->query) {
            $query = [
                'bool' => [
                    'must' => [
                        [
                            'bool' => [
                                'minimum_should_match' => 1,
                                'should' => [
                                    [
                                        'query_string' => [
                                            'default_operator' => 'AND',
                                            'fields' => [
                                                'name_en',
                                                'name_en.folded',
                                                'name_es',
                                                'name_es.folded',
                                            ],
                                            'query' => $params->query,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ];
        } else {
            $query = [];
        }

        return $query;
    }

    /**
     * @param string $search
     * @param string $locale
     *
     * @return array
     */
    protected function getElasticAutoSuggestBusinessSearchQuery($search, $locale)
    {
        $query = [
            'multi_match' => [
                'type' => 'most_fields',
                'query' => AdminHelper::convertAccentedString($search),
                'fields' => [
                    'auto_suggest_' . strtolower($locale),
                    'auto_suggest_' . strtolower($locale) . '.folded',
                    'name.single_characters',
                ],
            ],
        ];

        return $query;
    }

    /**
     * @param SearchDTO $params
     *
     * @return array
     */
    protected function getElasticCommonFilters(SearchDTO $params)
    {
        $filters = [];

        $categoryFilter = $params->getCategoryFilter();

        if ($categoryFilter) {
            $filters[] = [
                'match' => [
                    'categories_ids' => (int) $categoryFilter,
                ],
            ];
        }

        $neighborhood = $params->getNeighborhood();

        if ($neighborhood) {
            $filters[] = [
                'match' => [
                    'neighborhood_ids' => (int) $neighborhood,
                ],
            ];
        }

        return $filters;
    }

    /**
     * @param SearchDTO $params
     * @param array     $ids
     * @param string    $field
     * @param int       $minimumMatch
     *
     * @return array
     */
    protected function getElasticMultipleFilters(SearchDTO $params, $ids, $field, $minimumMatch)
    {
        $filters = [];

        if ($ids) {
            if ($minimumMatch) {
                $filters['bool'] = [
                    'minimum_should_match' => (int) $minimumMatch,
                    'should' => [],
                ];
            }

            foreach ($ids as $id) {
                $filter = [
                    'match' => [
                        $field => $id,
                    ],
                ];

                if ($minimumMatch) {
                    $filters['bool']['should'][] = $filter;
                } else {
                    $filters[] = $filter;
                }
            }
        }

        if ($minimumMatch) {
            return [
                $filters,
            ];
        } else {
            return $filters;
        }
    }

    /**
     * @param EmergencySearchDTO $params
     *
     * @return array
     */
    protected function getElasticEmergencyCatalogFilters(EmergencySearchDTO $params)
    {
        $filters = [
            [
                'match' => [
                    'category_id' => (int) $params->categoryId,
                ],
            ],
            [
                'match' => [
                    'area_id' => (int) $params->areaId,
                ],
            ]
        ];

        if ($params->characterFilter) {
            $filters[] = [
                'match' => [
                    'first_symbol' => $params->characterFilter,
                ],
            ];
        }

        if ($params->serviceIds) {
            foreach ($params->serviceIds as $serviceId) {
                $filters[] = [
                    'match' => [
                        'service_ids' => (int) $serviceId,
                    ],
                ];
            }
        }

        return $filters;
    }

    /**
     * @param array $searchQuery
     * @param array $locationQuery
     *
     * @return array
     */
    protected function addElasticLocationQuery($searchQuery, $locationQuery)
    {
        if ($locationQuery) {
            $searchQuery['query']['bool']['must'][] = $locationQuery;
        }

        return $searchQuery;
    }

    /**
     * @param array $searchQuery
     * @param array $filters
     *
     * @return array
     */
    protected function addElasticFiltersQuery($searchQuery, $filters)
    {
        foreach ($filters as $filter) {
            $searchQuery['query']['bool']['must'][] = $filter;
        }

        return $searchQuery;
    }

    /**
     * @param array $searchQuery
     * @param array $locationFilter
     *
     * @return array
     */
    protected function addElasticLocationFilterQuery($searchQuery, $locationFilter)
    {
        if ($locationFilter) {
            $searchQuery['query']['bool']['filter'][] = $locationFilter;
        }

        return $searchQuery;
    }

    /**
     * @param array $searchQuery
     * @param array $excludeIds
     *
     * @return array
     */
    protected function addElasticExcludeIdsQuery($searchQuery, $excludeIds)
    {
        if ($excludeIds) {
            $searchQuery['query']['bool']['must_not'][] = [
                'ids' => [
                    'values' => $excludeIds,
                ],
            ];
        }

        return $searchQuery;
    }

    /**
     * @param array $searchQuery
     * @param $excludeQuery
     *
     * @return array
     */
    protected function addElasticExcludeQuery($searchQuery, $excludeQuery)
    {
        if ($excludeQuery) {
            $searchQuery['query']['bool']['must_not'][] = $excludeQuery;
        }

        return $searchQuery;
    }

    /**
     * @param array $searchQuery
     * @param int   $size
     *
     * @return array
     */
    protected function addElasticAdsRandomAggregationQuery($searchQuery, $size)
    {
        $searchQuery['aggs'] = [
            'ads' => [
                'terms' => [
                    'field' => 'parent_id',
                    'order' => [
                        'rand' => 'desc',
                    ],
                    'size' => (int) $size,
                ],
                'aggs' => [
                    'rand' => [
                        'max' => [
                            'script' => 'Math.random()',
                        ],
                    ],
                ],
            ],
        ];

        return $searchQuery;
    }

    /**
     * @param array $searchQuery
     * @param array   $sort
     *
     * @return array
     */
    protected function addElasticSortQuery($searchQuery, $sort)
    {
        if ($sort) {
            $searchQuery['sort'] = $sort;
        }

        return $searchQuery;
    }

    /**
     * @param array     $searchQuery
     * @param array     $query
     *
     * @return array
     */
    protected function addElasticMainQuery($searchQuery, $query)
    {
        if ($query) {
            $searchQuery['query'] = $query;
        }

        return $searchQuery;
    }

    /**
     * @param SearchDTO $params
     *
     * @return array
     */
    protected function getElasticSortQuery(SearchDTO $params)
    {
        $sort = $this->getElasticSubscriptionSortQuery();

        if (SearchDataUtil::ORDER_BY_DISTANCE == $params->getOrderBy()) {
            $sort = array_merge($sort, $this->getElasticGeoSortQuery($params));
            $sort = array_merge($sort, $this->getElasticScoreSortQuery());
        } else {
            $sort = array_merge($sort, $this->getElasticScoreSortQuery());
            $sort = array_merge($sort, $this->getElasticGeoSortQuery($params));
        }

        return $sort;
    }

    /**
     * @param SearchDTO $params
     *
     * @return array
     */
    protected function getElasticGeoSortQuery(SearchDTO $params)
    {
        $coordinates = $params->getCurrentCoordinates();

        return [
            '_geo_distance' => [
                'location' => [
                    'lat' => $coordinates['lat'],
                    'lon' => $coordinates['lng'],
                ],
                'unit' => 'mi',
                'order' => 'asc',
            ],
        ];
    }

    /**
     * @param EmergencySearchDTO $params
     *
     * @return array
     */
    protected function getEmergencyCatalogElasticSortQuery(EmergencySearchDTO $params)
    {
        $sort = [];

        if (SearchDataUtil::EMERGENCY_ORDER_BY_DISTANCE == $params->orderBy) {
            $sort = array_merge($sort, $this->getEmergencyElasticGeoSortQuery($params));
            $sort = array_merge($sort, $this->getElasticEmergencyTitleSortQuery());
        } else {
            $sort = array_merge($sort, $this->getElasticEmergencyTitleSortQuery());
            $sort = array_merge($sort, $this->getEmergencyElasticGeoSortQuery($params));
        }

        return $sort;
    }

    /**
     * @param EmergencySearchDTO $params
     *
     * @return array
     */
    protected function getEmergencyElasticGeoSortQuery(EmergencySearchDTO $params)
    {
        if ($params->sortingByDistanceAvailable()) {
            $sort = [
                '_geo_distance' => [
                    'location' => [
                        'lat' => $params->lat,
                        'lon' => $params->lng,
                    ],
                    'unit' => 'mi',
                    'order' => 'asc',
                ],
            ];
        } else {
            $sort = [];
        }

        return $sort;
    }

    /**
     * @return array
     */
    protected function getElasticSubscriptionSortQuery()
    {
        return [
            'subscr_rank' => [
                'order' => 'desc',
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getElasticScoreSortQuery()
    {
        return [
            '_score' => [
                'order' => 'desc',
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getElasticEmergencyTitleSortQuery()
    {
        return [
            'title' => [
                'order'  => 'asc',
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getElasticRandomSortQuery()
    {
        return [
            '_script' => [
                'script' => 'Math.random()',
                'type'   => 'number',
                'order'  => 'asc',
            ],
        ];
    }

    /**
     * @param int   $page
     * @param int   $limit
     * @param bool  $trackScore
     *
     * @return array
     */
    protected function getElasticBaseQuery($page = 0, $limit = 0, $trackScore = true)
    {
        return [
            'from' => ($page - 1) * $limit,
            'size' => $limit,
            'track_scores' => $trackScore,
        ];
    }

    /**
     * @param string $query
     * @param string $locale
     * @param int|bool $limit
     * @param int $offset
     *
     * @return array
     */
    protected function getElasticAutoSuggestSearchQuery($query, $locale, $limit = false, $offset = 1)
    {
        if (!$limit) {
            $limit = self::AUTO_SUGGEST_MAX_BUSINESSES_COUNT;
        }

        $sort = $this->getElasticScoreSortQuery();
        $autoSuggestQuery = $this->getElasticAutoSuggestBusinessSearchQuery($query, $locale);

        $searchQuery = $this->getElasticBaseQuery($offset, $limit);
        $searchQuery = $this->addElasticMainQuery($searchQuery, $autoSuggestQuery);
        $searchQuery = $this->addElasticSortQuery($searchQuery, $sort);

        return $searchQuery;
    }

    /**
     * @param BusinessProfile $businessProfile
     *
     * @return array
     */
    public function buildBusinessProfileElasticData(BusinessProfile $businessProfile)
    {
        $businessSubscription     = $businessProfile->getSubscription();

        if (!$businessSubscription || $businessSubscription->getStatus() != StatusInterface::STATUS_ACTIVE ||
            !$businessProfile->getIsActive()
        ) {
            return false;
        }

        $enLocale   = LocaleHelper::LOCALE_EN;
        $esLocale   = LocaleHelper::LOCALE_ES;
        $categories = [
            $enLocale => [],
            $esLocale => [],
        ];

        $categoryIds = [];

        foreach ($businessProfile->getCategories() as $category) {
            $categoryEn = $category->getTranslation('name', $enLocale);
            $categoryEs = $category->getTranslation('name', $esLocale);

            $categories[$enLocale][] = SearchDataUtil::sanitizeElasticSearchQueryString($categoryEn);
            $categories[$esLocale][] = SearchDataUtil::sanitizeElasticSearchQueryString($categoryEs);
            $categoryIds[] = $category->getId();
        }

        $neighborhoodIds = [];

        foreach ($businessProfile->getNeighborhoods() as $neighborhood) {
            $neighborhoodIds[] = $neighborhood->getId();
        }

        $localityIds = [];

        foreach ($businessProfile->getLocalities() as $locality) {
            $localityIds[] = $locality->getId();
        }

        if ($businessProfile->getCatalogLocality()) {
            $localityIds[] = $businessProfile->getCatalogLocality()->getId();
        }

        $businessProfileName = SearchDataUtil::sanitizeElasticSearchQueryString($businessProfile->getName());
        $businessProfileDescEn = SearchDataUtil::sanitizeElasticSearchQueryString($businessProfile->getDescriptionEn());
        $businessProfileDescEs = SearchDataUtil::sanitizeElasticSearchQueryString($businessProfile->getDescriptionEs());
        $businessProfileCity = SearchDataUtil::sanitizeElasticSearchQueryString($businessProfile->getCity());

        $businessProfileProdEn = SearchDataUtil::sanitizeElasticSearchQueryString(
            $businessProfile->getTranslation(BusinessProfile::BUSINESS_PROFILE_FIELD_PRODUCT, $enLocale)
        );
        $businessProfileProdEs = SearchDataUtil::sanitizeElasticSearchQueryString(
            $businessProfile->getTranslation(BusinessProfile::BUSINESS_PROFILE_FIELD_PRODUCT, $esLocale)
        );

        if ($businessProfile->getMilesOfMyBusiness()) {
            $milesOfMyBusiness = $businessProfile->getMilesOfMyBusiness();
        } else {
            $milesOfMyBusiness = BusinessProfile::DEFAULT_MILES_FROM_MY_BUSINESS;
        }

        $keywords = [];

        if ($businessProfile->getSubscriptionPlanCode() > SubscriptionPlanInterface::CODE_FREE and
            $businessProfile->getKeywordText()
        ) {
            $keywordsData = explode(BusinessProfile::KEYWORD_DELIMITER, $businessProfile->getKeywordText());

            foreach ($keywordsData as $keyword) {
                $keywords[] = SearchDataUtil::sanitizeElasticSearchQueryString($keyword);
            }
        }

        $data = [
            'id'                   => $businessProfile->getId(),
            'name'                 => $businessProfileName,
            'city'                 => $businessProfileCity,
            'description_en'       => $businessProfileDescEn,
            'description_es'       => $businessProfileDescEs,
            'products_en'          => $businessProfileProdEn,
            'products_es'          => $businessProfileProdEs,
            'miles_of_my_business' => $milesOfMyBusiness,
            'categories_en'        => $categories[$enLocale],
            'categories_es'        => $categories[$esLocale],
            'keywords'             => $keywords,
            'auto_suggest_en'      => [$businessProfile->getNameEn()],
            'auto_suggest_es'      => [$businessProfile->getNameEs()],
            'location'             => [
                'lat' => $businessProfile->getLatitude(),
                'lon' => $businessProfile->getLongitude(),
            ],
            'service_areas_type'   => $businessProfile->getServiceAreasType(),
            'locality_ids'         => $localityIds,
            'subscr_rank'          => $businessProfile->getSubscriptionPlanCode(),
            'neighborhood_ids'     => $neighborhoodIds,
            'categories_ids'       => $categoryIds,
        ];

        return $data;
    }

    /**
     * @param string $locale
     *
     * @return array
     */
    public function getBusinessSearchFields($locale)
    {
        return [
            'name^6',
            'name.folded^5',
            'name.single_characters^3',
            'keywords^5',
            'keywords.folded^5',
            'categories_en^5',
            'categories_en.folded^5',
            'categories_es^5',
            'categories_es.folded^5',
            'products_' . strtolower($locale) . '^0',
            'products_' . strtolower($locale) . '.folded^0',
        ];
    }

    /**
     * @param $extraSearch  BusinessProfileExtraSearch
     * @param $data         array
     *
     * @return array
     */
    public function buildBusinessProfileChildElasticData($extraSearch, $data)
    {
        $enLocale   = LocaleHelper::LOCALE_EN;
        $esLocale   = LocaleHelper::LOCALE_ES;
        $categories = [
            $enLocale => [],
            $esLocale => [],
        ];

        $categoryIds = [];

        foreach ($extraSearch->getCategories() as $category) {
            $categoryEn = $category->getTranslation('name', $enLocale);
            $categoryEs = $category->getTranslation('name', $esLocale);

            $categories[$enLocale][] = SearchDataUtil::sanitizeElasticSearchQueryString($categoryEn);
            $categories[$esLocale][] = SearchDataUtil::sanitizeElasticSearchQueryString($categoryEs);
            $categoryIds[] = $category->getId();
        }

        $localityIds = [];

        foreach ($extraSearch->getLocalities() as $locality) {
            $localityIds[] = $locality->getId();
        }

        if ($extraSearch->getMilesOfMyBusiness()) {
            $milesOfMyBusiness = $extraSearch->getMilesOfMyBusiness();
        } else {
            $milesOfMyBusiness = BusinessProfile::DEFAULT_MILES_FROM_MY_BUSINESS;
        }

        $data['id'] = $extraSearch->getId();
        $data['miles_of_my_business'] = $milesOfMyBusiness;
        $data['categories_en'] = $categories[$enLocale];
        $data['categories_es'] = $categories[$esLocale];
        $data['service_areas_type'] = $extraSearch->getServiceAreasType();
        $data['locality_ids'] = $localityIds;

        $data['subscr_rank'] = SubscriptionPlanInterface::CODE_SUPER_VM;
        $data['parent_id'] = $extraSearch->getBusinessProfile()->getId();
        $data['categories_ids'] = $categoryIds;

        return $data;
    }

    /**
     * @return array
     */
    public static function getBusinessAdElasticSearchIndexParams(): array
    {
        $params = self::getBusinessElasticSearchIndexParams();

        $params['parent_id'] = [
            'type' => 'integer'
        ];

        return $params;
    }

    /**
     * @return array
     */
    public static function getBusinessElasticSearchIndexParams(): array
    {
        return [
            'auto_suggest_en' => [
                'type' => 'text',
                'analyzer' => 'autocomplete',
                'search_analyzer' => 'autocomplete_search',
                'fields' => [
                    'folded' => [
                        'type' => 'text',
                        'analyzer' => 'folding',
                    ],
                ],
            ],
            'auto_suggest_es' => [
                'type' => 'text',
                'analyzer' => 'autocomplete',
                'search_analyzer' => 'autocomplete_search',
                'fields' => [
                    'folded' => [
                        'type' => 'text',
                        'analyzer' => 'folding',
                    ],
                ],
            ],
            'name' => [
                'type' => 'text',
                'analyzer' => 'autocomplete',
                'search_analyzer' => 'autocomplete_search',
                'fields' => [
                    'folded' => [
                        'type' => 'text',
                        'analyzer' => 'folding',
                    ],
                    'single_characters' => [
                        'type' => 'text',
                        'analyzer' => 'single_characters',
                        'search_analyzer' => 'autocomplete_search',
                    ]
                ],
            ],
            'city' => [
                'type' => 'text',
                'analyzer' => 'autocomplete',
                'search_analyzer' => 'autocomplete_search',
                'fields' => [
                    'folded' => [
                        'type' => 'text',
                        'analyzer' => 'folding',
                    ],
                ],
            ],
            'categories_en' => [
                'type' => 'text',
                'analyzer' => 'autocomplete',
                'search_analyzer' => 'autocomplete_search',
                'fields' => [
                    'folded' => [
                        'type' => 'text',
                        'analyzer' => 'folding',
                    ],
                ],
            ],
            'categories_es' => [
                'type' => 'text',
                'analyzer' => 'autocomplete',
                'search_analyzer' => 'autocomplete_search',
                'fields' => [
                    'folded' => [
                        'type' => 'text',
                        'analyzer' => 'folding',
                    ],
                ],
            ],
            'description_en' => [
                'type' => 'text',
                'analyzer' => 'autocomplete',
                'search_analyzer' => 'autocomplete_search',
                'fields' => [
                    'folded' => [
                        'type' => 'text',
                        'analyzer' => 'folding',
                    ],
                ],
            ],
            'description_es' => [
                'type' => 'text',
                'analyzer' => 'autocomplete',
                'search_analyzer' => 'autocomplete_search',
                'fields' => [
                    'folded' => [
                        'type' => 'text',
                        'analyzer' => 'folding',
                    ],
                ],
            ],
            'products_en' => [
                'type' => 'text',
                'analyzer' => 'autocomplete',
                'search_analyzer' => 'autocomplete_search',
                'fields' => [
                    'folded' => [
                        'type' => 'text',
                        'analyzer' => 'folding',
                    ],
                ],
            ],
            'products_es' => [
                'type' => 'text',
                'analyzer' => 'autocomplete',
                'search_analyzer' => 'autocomplete_search',
                'fields' => [
                    'folded' => [
                        'type' => 'text',
                        'analyzer' => 'folding',
                    ],
                ],
            ],
            'keywords' => [
                'type' => 'text',
                'analyzer' => 'autocomplete',
                'search_analyzer' => 'autocomplete_search',
                'fields' => [
                    'folded' => [
                        'type' => 'text',
                        'analyzer' => 'folding',
                    ],
                ],
            ],
            'location' => [
                'type' => 'geo_point'
            ],
            'miles_of_my_business' => [
                'type' => 'integer'
            ],
            'subscr_rank' => [
                'type' => 'integer'
            ],
            'service_areas_type' => [
                'type'  => 'keyword',
            ],
        ];
    }

    public function handleBusinessElasticSync(): void
    {
        $bpIndex = $this->elasticSearchManager->createElasticSearchIndex(
            BusinessProfile::ELASTIC_INDEX,
            self::getBusinessElasticSearchIndexParams()
        );

        $bpAdIndex = $this->elasticSearchManager->createElasticSearchIndex(
            BusinessProfile::ELASTIC_INDEX_AD,
            self::getBusinessAdElasticSearchIndexParams()
        );

        if ($bpIndex && $bpAdIndex) {
            $businesses = $this->getRepository()->getUpdatedBusinessProfilesIterator();

            $iDoctrine = 0;
            $batchDoctrine = 20;

            $iElastic = 0;
            $batchElastic = $this->elasticSearchManager->getIndexingPage();

            $data = [];

            foreach ($businesses as $businessRow) {
                /* @var $business BusinessProfile */
                $business = current($businessRow);

                $item = $this->buildBusinessProfileElasticData($business);

                $this->handleBusinessAdsElasticData($business, $item);

                if ($item) {
                    $data[] = $item;
                } else {
                    $this->removeItemFromElastic(BusinessProfile::ELASTIC_INDEX, $business->getId());
                }

                $business->setIsUpdated(false);
                $business->setHasImages($this->checkBusinessHasImages($business));

                if (($iElastic % $batchElastic) === 0) {
                    $this->elasticSearchManager->addElasticBulkItemData(BusinessProfile::ELASTIC_INDEX, $data);
                    $data = [];
                }

                if (($iDoctrine % $batchDoctrine) === 0) {
                    $this->em->flush();
                    $this->em->clear();
                }

                $iElastic ++;
                $iDoctrine ++;
            }

            if ($data) {
                $this->elasticSearchManager->addElasticBulkItemData(BusinessProfile::ELASTIC_INDEX, $data);
            }

            $this->em->flush();
        }
    }

    /**
     * @param $business     BusinessProfile
     * @param $parentData   array
     */
    protected function handleBusinessAdsElasticData($business, $parentData): void
    {
        $ads = [];

        if (!$business->getExtraSearches()->isEmpty()) {
            foreach ($business->getExtraSearches() as $extraSearch) {
                if ($parentData &&
                    $business->getSubscriptionPlanCode() >= SubscriptionPlanInterface::CODE_PREMIUM_PLATINUM
                ) {
                    $ads[] = $this->buildBusinessProfileChildElasticData($extraSearch, $parentData);
                } else {
                    $this->removeItemFromElastic(BusinessProfile::ELASTIC_INDEX_AD, $extraSearch->getId());
                }
            }
        }

        if ($ads) {
            $this->elasticSearchManager->addElasticBulkItemData(BusinessProfile::ELASTIC_INDEX_AD, $ads);
        }
    }

    public function handleCategoryElasticSync(): void
    {
        $index = $this->elasticSearchManager->createElasticSearchIndex(
            Category::ELASTIC_INDEX,
            CategoryManager::getCategoryElasticSearchIndexParams()
        );

        if ($index) {
            $categories = $this->categoryManager->getUpdatedCategoriesIterator();

            $countDoctrine = 0;
            $batchDoctrine = 20;

            $countElastic = 0;
            $batchElastic = $this->elasticSearchManager->getIndexingPage();

            $data = [];

            foreach ($categories as $categoryRow) {
                /* @var $category Category */
                $category = current($categoryRow);

                $item = $this->categoryManager->buildCategoryElasticData($category);

                if ($item) {
                    $data[] = $item;
                } else {
                    $this->removeItemFromElastic(Category::ELASTIC_INDEX, $category->getId());
                }

                $category->setIsUpdated(false);

                if (($countElastic % $batchElastic) === 0) {
                    $this->elasticSearchManager->addElasticBulkItemData(Category::ELASTIC_INDEX, $data);
                    $data = [];
                }

                if (($countDoctrine % $batchDoctrine) === 0) {
                    $this->em->flush();
                    $this->em->clear();
                }

                $countElastic ++;
                $countDoctrine ++;
            }

            if ($data) {
                $this->elasticSearchManager->addElasticBulkItemData(Category::ELASTIC_INDEX, $data);
            }

            $this->em->flush();
        }
    }

    public function handleLocalityElasticSync(): void
    {
        $index = $this->elasticSearchManager->createElasticSearchIndex(
            Locality::ELASTIC_INDEX,
            LocalityManager::getLocalityElasticSearchIndexParams()
        );

        if ($index) {
            $localities = $this->localityManager->getUpdatedLocalitiesIterator();

            $countDoctrine = 0;
            $batchDoctrine = 20;

            $countElastic = 0;
            $batchElastic = $this->elasticSearchManager->getIndexingPage();

            $data = [];

            foreach ($localities as $localityRow) {
                /* @var $locality Locality */
                $locality = current($localityRow);

                $item = $this->localityManager->buildLocalityElasticData($locality);

                if ($item) {
                    $data[] = $item;
                } else {
                    $this->removeItemFromElastic(Locality::ELASTIC_INDEX, $locality->getId());
                }

                $locality->setIsUpdated(false);

                if (($countElastic % $batchElastic) === 0) {
                    $this->elasticSearchManager->addElasticBulkItemData(Locality::ELASTIC_INDEX, $data);
                    $data = [];
                }

                if (($countDoctrine % $batchDoctrine) === 0) {
                    $this->em->flush();
                    $this->em->clear();
                }

                $countElastic ++;
                $countDoctrine ++;
            }

            if ($data) {
                $this->elasticSearchManager->addElasticBulkItemData(Locality::ELASTIC_INDEX, $data);
            }

            $this->em->flush();
        }
    }

    public function handleEmergencyBusinessElasticSync(): void
    {
        $index = $this->elasticSearchManager->createElasticSearchIndex(
            EmergencyBusiness::ELASTIC_INDEX,
            EmergencyManager::getEmergencyBusinessElasticSearchIndexParams()
        );

        if ($index) {
            $businesses = $this->emergencyManager->getUpdatedLocalitiesIterator();

            $countDoctrine = 0;
            $batchDoctrine = 20;

            $countElastic = 0;
            $batchElastic = $this->elasticSearchManager->getIndexingPage();

            $data = [];

            foreach ($businesses as $businessRow) {
                /* @var $business EmergencyBusiness */
                $business = current($businessRow);

                $item = $this->emergencyManager->buildEmergencyBusinessElasticData($business);

                if ($item) {
                    $data[] = $item;
                } else {
                    $this->removeItemFromElastic(EmergencyBusiness::ELASTIC_INDEX, $business->getId());
                }

                $business->setIsUpdated(false);

                if (($countElastic % $batchElastic) === 0) {
                    $this->elasticSearchManager->addElasticBulkItemData(EmergencyBusiness::ELASTIC_INDEX, $data);
                    $data = [];
                }

                if (($countDoctrine % $batchDoctrine) === 0) {
                    $this->em->flush();
                    $this->em->clear();
                }

                $countElastic ++;
                $countDoctrine ++;
            }

            if ($data) {
                $this->elasticSearchManager->addElasticBulkItemData(EmergencyBusiness::ELASTIC_INDEX, $data);
            }

            $this->em->flush();
        }
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function shuffleSearchResult($data)
    {
        $raw = [];

        foreach ($data as $item) {
            // convert to string to avoid cast of float array keys to integer
            $plan = (string)$item['plan'];

            $raw[$plan][] = $item['id'];
        }

        $result = [];

        foreach ($raw as $code => $items) {
            if ($code != SubscriptionPlanInterface::CODE_FREE) {
                shuffle($items);
            }

            $result = array_merge($result, $items);
        }

        return $result;
    }

    /**
     * @return int
     */
    public function updatedManagedBusinessesCounter()
    {
        $updated = 0;

        $businessesData = $this->em->getRepository(User::class)->getManagedBusinessesData();

        foreach ($businessesData as $item) {
            $user = $this->em->getReference(User::class, $item['userId']);
            $user->setBusinessesCount($item['cnt']);

            $updated++;
        }

        $this->em->flush();

        return $updated;
    }

    /**
     * @param BusinessProfile $businessProfile
     *
     * @return bool
     */
    public function getClaimButtonPermitted($businessProfile)
    {
        if ($this->getCurrentUser() and !$businessProfile->getUser()) {
            return true;
        }

        return false;
    }

    /**
     * @return User|null
     */
    protected function getCurrentUser()
    {
        $currentUser = null;

        $tokenStorage = $this->container->get('security.token_storage');

        if ($tokenStorage->getToken() !== null and $tokenStorage->getToken()->getUser() instanceof User) {
            $currentUser = $tokenStorage->getToken()->getUser();
        }

        return $currentUser;
    }

    /**
     * @param BusinessProfile $business
     *
     * @return bool
     */
    protected function checkBusinessHasImages($business)
    {
        if ($business->getLogo() or $business->getBackground() or !$business->getImages()->isEmpty()) {
            return true;
        }

        return false;
    }

    /**
     * @param array $filter
     *
     * @return \Exporter\Source\SourceIteratorInterface
     */
    public function getBusinessProfileExportDataIterator($filter = [])
    {
        $admin = $this->container->get('domain_business.admin.business_profile');

        $params['filter'] = $filter;

        $request = new Request($params);

        $admin->setRequest($request);

        $iterator = $admin->getDataSourceIterator();
        $iterator->setDateTimeFormat(AdminHelper::DATETIME_FORMAT);

        unset($admin, $params, $request);

        return $iterator;
    }

    /**
     * @param int $id
     *
     * @return Media|null
     */
    public function getBusinessGalleryMediaById($id)
    {
        return $this->em->getRepository(Media::class)->find($id);
    }

    public function getCollectionItemValuesByIds($ids, $class)
    {
        $values = [];

        foreach ($ids as $key => $id) {
            if (!$id) {
                continue;
            }

            $items = $this->em->getRepository($class)->getValuesByIds($id);

            foreach ($items as $item) {
                $values[$key][] = [
                    'id'    => $item->getId(),
                    'title' => $item->getTitle(),
                ];
            }
        }

        return $values;
    }

    /**
     * @param string $name
     * @param string $city
     * @param int    $id
     *
     * @return BusinessProfile[]
     */
    public function getSimilarBusinesses($name, $city, $id)
    {
        $businessSearchQuery = $this->getElasticSimilarBusinessSearchQuery($name, $id, $city);

        $searchQuery = $this->getElasticBaseQuery(1, BusinessProfileAdmin::MAX_VALIDATION_RESULT);
        $searchQuery = $this->addElasticMainQuery($searchQuery, $businessSearchQuery);

        $response = $this->searchElastic(BusinessProfile::ELASTIC_INDEX, $searchQuery);

        return $this->getBusinessDataFromElasticResponse($response);
    }

    /**
     * @param array $phones
     * @param int   $id
     *
     * @return BusinessProfile[]
     */
    public function getSimilarBusinessesByPhones($phones, $id)
    {
        $items = $this->getRepository()->getSimilarBusinessesByPhones($phones, $id);

        return $items;
    }
}
