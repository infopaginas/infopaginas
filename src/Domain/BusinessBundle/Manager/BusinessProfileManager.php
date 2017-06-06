<?php

namespace Domain\BusinessBundle\Manager;

use AntiMattr\GoogleBundle\Analytics;
use AntiMattr\GoogleBundle\Analytics\Impression;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
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
use Domain\BusinessBundle\Repository\BusinessReviewRepository;
use Domain\BusinessBundle\Util\ChangeSetCalculator;
use Domain\BusinessBundle\Util\Task\PhoneChangeSetUtil;
use Domain\BusinessBundle\Util\SlugUtil;
use Domain\BusinessBundle\Util\Task\RelationChangeSetUtil;
use Domain\BusinessBundle\Util\Task\TranslationChangeSetUtil;
use Domain\BusinessBundle\Util\Task\WorkingHoursChangeSetUtil;
use Domain\ReportBundle\Util\DatesUtil;
use Domain\SearchBundle\Util\SearchDataUtil;
use FOS\UserBundle\Model\UserInterface;
use Gedmo\Translatable\TranslatableListener;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Oxa\ElasticSearchBundle\Manager\ElasticSearchManager;
use Oxa\GeolocationBundle\Utils\GeolocationUtils;
use Oxa\ManagerArchitectureBundle\Model\Manager\Manager;
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
use Symfony\Component\PropertyAccess\PropertyAccess;
use Oxa\GeolocationBundle\Model\Geolocation\LocationValueObject;
use Domain\SearchBundle\Model\DataType\SearchDTO;
use Domain\SearchBundle\Model\DataType\DCDataDTO;

/**
 * Class BusinessProfileManager
 * @package Domain\BusinessBundle\Manager
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

        $this->translatableListener = $container->get('sonata_translation.listener.translatable');

        $this->formFactory = $container->get('form.factory');

        $this->sonataMediaManager = $container->get('sonata.media.manager.media');

        $this->analytics = $container->get('google.analytics');

        $this->elasticSearchManager = $container->get('oxa_elastic_search.manager.search');

        $this->elasticSearchManager->setDocumentType(BusinessProfile::ELASTIC_DOCUMENT_TYPE);
    }

    public function searchByPhraseAndLocation(string $phrase, LocationValueObject $location, $categoryFilter = null)
    {
        $locationName = $location->name;
        if (empty($locationName)) {
            $locationName = self::DEFAULT_LOCALE_NAME;
        }

        // TODO Move to filtering functionality
        $phrase = preg_replace("/[^a-zA-Z0-9\s]+/", "", $phrase);
        return $this->getRepository()->searchWithQueryBuilder($phrase, $locationName, $categoryFilter);
    }

    public function searchAutosuggestByPhraseAndLocation($query, $locale)
    {
        $categories = $this->searchCategoryAutoSuggestInElastic($query, $locale);
        $businessProfiles = $this->searchBusinessAutoSuggestInElastic($query, $locale);

        $result = array_merge($categories, $businessProfiles);

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

    public function searchWithMapByPhraseAndLocation(string $phrase, string $location)
    {
        if (!$location) {
            $location = self::DEFAULT_LOCALE_NAME;
        }

        // TODO Move to filtering functionality
        $phrase = preg_replace("/[^a-zA-Z0-9\s]+/", "", $phrase);
        return $this->getRepository()->searchWithQueryBuilder($phrase, $location);
    }

    public function getLocationMarkersFromProfileData(array $profilesList)
    {
        $profilesArray = [];

        /** @var BusinessProfile $profile */
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
                "id"            => $profile->getId(),
                "name"          => $profile->getName(),
                "address"       => $profile->getShortAddress(),
                "reviewsCount"  => $profile->getBusinessReviewsCount(),
                "logo"          => $logoPath,
                "background"    => $backgndPath,
                "latitude"      => $profile->getLatitude(),
                "longitude"     => $profile->getLongitude(),
                'rating'        => $this->calculateReviewsAvgRatingForBusinessProfile($profile),
                "profileUrl"    => $this->container->get('router')->generate('domain_business_profile_view', [
                    'slug'          => $profile->getSlug(),
                    'citySlug'      => $profile->getCatalogLocality()->getSlug(),
                ]),
            ];
        }

        if (!$profilesArray) {
            $profilesArray[] = $this->getDefaultLocationMarkers(false);
        }

        return json_encode($profilesArray);
    }

    public function getLocationMarkersFromLocalityData($localities)
    {
        $data = [];

        /** @var Locality $locality */
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

    public function getDefaultLocationMarkers($isEncoded = true)
    {
        $defaultCenterCoordinates = $this->container->getParameter('google_map_default_center');
        $defaultCenterName        = $this->container->getParameter('google_map_default_center_name');
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

    public function search(SearchDTO $searchParams, string $locale)
    {
        $searchResultsData = $this->searchBusinessInElastic($searchParams, $locale);

        return $searchResultsData;
    }

    public function searchCatalog(SearchDTO $searchParams)
    {
        $searchResultsData = $this->searchCatalogBusinessInElastic($searchParams);

        return $searchResultsData;
    }

    /**
     * @param int $id
     * @param string $locale
     * @return null|object
     */
    public function find(int $id, string $locale = 'en')
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
     * @param string $locale
     */
    public function saveProfile(BusinessProfile $businessProfile, string $locale = 'en')
    {
        if (!$businessProfile->getId()) {
            $businessProfile->setIsActive(false);
        }

        foreach ($businessProfile->getImages() as $gallery) {
            $businessProfile->removeImage($gallery);
            $gallery->setBusinessProfile($businessProfile);
            $this->getEntityManager()->persist($gallery);
            $businessProfile->addImage($gallery);
        }
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
                                    $this->getVideoManager()->removeMedia($dataOld->id);

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
                            $this->getVideoManager()->removeMedia($businessProfile->getVideo()->getId());
                        }

                        $accessor->setValue($businessProfile, $change->getFieldName(), null);
                    }

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

                        if ($dataNew) {
                            if ($dataNew->id) {
                                $translation = $this->em->getRepository(BusinessProfileTranslation::class)
                                    ->find($dataNew->id);
                            } else {
                                $translation = new BusinessProfileTranslation();

                                $businessProfile->addTranslation($translation);

                                $this->em->persist($translation);
                            }

                            $translation->setField($dataNew->field);
                            $translation->setLocale($dataNew->locale);
                            $translation->setContent($dataNew->value);

                            $translation->setObject($businessProfile);
                        } elseif ($dataOld and $dataOld->id) {
                            $translation = $this->em->getRepository(BusinessProfileTranslation::class)
                                ->find($dataOld->id);

                            $this->em->remove($translation);
                        }
                    }
                    break;
            }
        }

        $this->getEntityManager()->flush();
    }

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
     * @return array
     */
    public function getBusinessProfileAdvertisementImages(BusinessProfile $businessProfile)
    {
        $subscriptionPlanCode = $businessProfile->getSubscriptionPlanCode();

        if ($subscriptionPlanCode > SubscriptionPlanInterface::CODE_PREMIUM_PLUS) {
            $advertisements = $this->getBusinessGalleryRepository()
                ->findBusinessProfileAdvertisementImages($businessProfile);

            return $advertisements;
        }

        return [];
    }

    /**
     * @param BusinessProfile $businessProfile
     * @return array
     */
    public function getBusinessProfilePhotoImages(BusinessProfile $businessProfile)
    {
        $subscriptionPlanCode = $businessProfile->getSubscriptionPlanCode();

        if ($subscriptionPlanCode > SubscriptionPlanInterface::CODE_PREMIUM_PLUS) {
            $photos = $this->getBusinessGalleryRepository()->findBusinessProfilePhotoImages($businessProfile);
            return $photos;
        }

        return [];
    }

    /**
     * @param BusinessProfile $businessProfile
     * @return null|object
     */
    public function getLastReviewForBusinessProfile(BusinessProfile $businessProfile)
    {
        $lastReview = $this->getBusinessProfileReviewsRepository()->findBusinessProfileLastReview($businessProfile);
        return $lastReview;
    }

    /**
     * @param BusinessProfile $businessProfile
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
     * @param array $businessProfiles
     */
    public function trackBusinessProfilesCollectionImpressions(array $businessProfiles)
    {
        /** @var BusinessProfile $businessProfile */
        foreach ($businessProfiles as $businessProfile) {
            $impression = new Impression();
            $impression->setSku($businessProfile->getSlug());
            $impression->setTitle($businessProfile->getName());
            $impression->setAction('detail');
            $impression->setBrand($businessProfile->getBrands());
            $impression->setCategory($businessProfile->getCategories()->first());
            $impression->setList('Search Results');

            $this->getGoogleAnalytics()->addImpression($impression);
        }
    }

    public function isAdUsageReportAllowedForBusiness(BusinessProfile $businessProfile)
    {
        $isAllowed = false;

        $code = $businessProfile->getSubscriptionPlanCode();

        if ($businessProfile->getDcOrderId() and $code >= SubscriptionPlanInterface::CODE_PRIORITY) {
            $isAllowed = true;
        }

        return $isAllowed;
    }

    public function findOneBusinessProfile()
    {
        return $this->getRepository()->findOneBy([]);
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
                    $country = $businessProfile->getCountry();

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

            if (!$businessProfile->getBusinessReviews()->isEmpty()) {
                $schemaItem['aggregateRating'] = [
                    '@type'       => 'AggregateRating',
                    'worstRating' => BusinessReview::RATING_MIN_VALUE,
                    'bestRating'  => BusinessReview::RATING_MAX_VALUE,
                    'ratingValue' => $businessProfile->getBusinessReviewsAvgMark(),
                    'ratingCount' => $businessProfile->getBusinessReviewsCount(),
                ];
            }

            if ($showAll) {
                $description = $businessProfile->getDescription();
                if ($description) {
                    $schemaItem['description'] = strip_tags($description);
                }

                $sameAs = $this->addSameAsUrl([], $businessProfile->getFacebookURL());
                $sameAs = $this->addSameAsUrl($sameAs, $businessProfile->getTwitterURL());
                $sameAs = $this->addSameAsUrl($sameAs, $businessProfile->getGoogleURL());
                $sameAs = $this->addSameAsUrl($sameAs, $businessProfile->getYoutubeURL());
                $sameAs = $this->addSameAsUrl($sameAs, $businessProfile->getInstagramURL());
                $sameAs = $this->addSameAsUrl($sameAs, $businessProfile->getTripAdvisorURL());

                $photos = $this->getBusinessProfilePhotoImages($businessProfile);

                foreach ($photos as $photo) {
                    $schemaItem['image'][] = $this->getMediaPublicUrl($photo->getMedia(), 'preview');
                }

                $lastReview = $this->getLastReviewForBusinessProfile($businessProfile);

                if ($lastReview) {
                    $schemaItem['review'] = $this->buildReviewItem($lastReview);
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

        return json_encode($schema, JSON_UNESCAPED_SLASHES);
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

        return json_encode([$schemaItem], JSON_UNESCAPED_SLASHES);
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
        if ($businessProfile->getWebsite()) {
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
        } elseif ($businessProfile->getWorkingHours()) {
            $schemaItem['openingHours'] = $businessProfile->getWorkingHours();
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
                'citySlug' => $businessProfile->getCatalogLocality()->getSlug(),
                'slug'     => $businessProfile->getSlug(),
            ],
            true
        );

        return $url;
    }

    public function getBusinessProfileSearchSeoData(
        $locality = null,
        $categories = [],
        $isCatalog = false
    ) {
        $translator  = $this->container->get('translator');
        $seoSettings = $this->container->getParameter('seo_custom_settings');

        $companyName          = $seoSettings['company_name'];
        $titleMaxLength       = $seoSettings['title_max_length'];
        $titleCategoryMaxLength = $seoSettings['title_category_max_length'];
        $titleLocalityMaxLength = $seoSettings['locality_length'];
        $descriptionMaxLength = $seoSettings['description_max_length'];
        $descriptionCategoriesMaxLength = $seoSettings['description_category_max_length'];
        $descriptionCategoriesSeparator = $seoSettings['description_category_separator'];
        $categoriesCut = $seoSettings['description_category_cut'];

        if ($isCatalog) {
            $seoTitle = $translator->trans('Catalog');
        } else {
            $seoTitle = $translator->trans('Search');
        }

        $categoryData = [];

        if ($categories) {
            $itemsCount = count($categories);
            $categoryData = [];
            $categoryMaxLength = floor($descriptionCategoriesMaxLength / $itemsCount);

            foreach ($categories as $category) {
                $categoryOutput = mb_substr($category, 0, $categoryMaxLength);

                if (mb_strlen($category) > $categoryMaxLength) {
                    $categoryOutput .= $categoriesCut;
                }

                $categoryData[] = $categoryOutput;
            }
        }

        $seoDescription = $translator->trans(
            'business_profile.seoDescription.search',
            [
                '{-categories-}' => implode($descriptionCategoriesSeparator, $categoryData),
                '{-locality-}'   => $locality,
            ],
            'messages'
        );

        if ($locality) {
            $seoTitle .= ' ' . $translator->trans('in') . ' ' . mb_substr($locality, 0, $titleLocalityMaxLength);
        }

        if (current($categories)) {
            $category1 = mb_substr(current($categories), 0, $titleCategoryMaxLength);
            $seoTitle .= ' ' . $translator->trans('for') . ' ' . $category1;
        }

        $seoTitle .=' | ' . $companyName;

        $seoData = [
            'seoTitle' => mb_substr($seoTitle, 0, $titleMaxLength),
            'seoDescription' => mb_substr($seoDescription, 0, $descriptionMaxLength),
        ];

        return $seoData;
    }

    public function getDefaultProfileCountry()
    {
        $country = $this->em->getRepository('DomainBusinessBundle:Address\Country')->findOneBy(
            ['shortName' => strtoupper(Country::PUERTO_RICO_SHORT_NAME)]
        );

        return $country;
    }

    protected function searchCatalogBusinessInElastic(SearchDTO $searchParams)
    {
        $searchQuery = $this->getCatalogBusinessSearchQuery($searchParams);

        $response = $this->searchBusinessElastic($searchQuery);
        $search = $this->getBusinessDataFromElasticResponse($response);

        $coordinates = $searchParams->getCurrentCoordinates();

        $search['data'] = array_map(function ($item) use ($searchParams, $coordinates) {
            $distance = GeolocationUtils::getDistanceForPoint(
                $coordinates['lat'],
                $coordinates['lng'],
                $item->getLatitude(),
                $item->getLongitude()
            );

            return $item->setDistance($distance);
        }, $search['data']);

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

            $item->setIsAd($isAd);

            return $item->setDistance($distance);
        }, $search['data']);

        return $search;
    }

    /**
     * @param $searchParams SearchDTO
     * @param $locale       string
     * @param $allowAds     bool
     *
     * @return array
     */
    protected function searchBusinessInElastic(SearchDTO $searchParams, $locale, $allowAds = true)
    {
        //randomize feature works only for relevance sorting ("Best match")
        $randomize   = $searchParams->randomizeAllowed();
        $coordinates = $searchParams->getCurrentCoordinates();

        if ($allowAds) {
            $searchAdQuery = $this->getElasticSearchQueryAd($searchParams, $locale);
            $responseAd = $this->searchBusinessAdElastic($searchAdQuery);

            $searchAd = $this->getBusinessAdDataFromElasticResponse($responseAd);
            $searchResultAds = $this->setBusinessDynamicValues($searchAd, $coordinates, true);

            $excludeIds = array_keys($searchAd['data']);
        } else {
            $excludeIds = [];
            $searchResultAds = [];
        }

        $searchQuery = $this->getElasticSearchQuery($searchParams, $locale, $excludeIds);

        $response = $this->searchBusinessElastic($searchQuery);

        $search = $this->getBusinessDataFromElasticResponse($response, $randomize);

        $search = $this->setBusinessDynamicValues($search, $coordinates);

        if ($allowAds and $searchResultAds) {
            foreach ($searchResultAds['data'] as $item) {
                array_unshift($search['data'], $item);
            }
        }

        return $search;
    }

    protected function searchBusinessAutoSuggestInElastic($query, $locale)
    {
        $searchQuery = $this->getElasticAutoSuggestSearchQuery($query, $locale);
        $response = $this->searchBusinessElastic($searchQuery);
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
     * @param string   $query
     * @param string   $locale
     * @param int|null $limit
     *
     * @return array
     */
    protected function searchCategoryAutoSuggestInElastic($query, $locale, $limit = null)
    {
        $searchQuery = $this->categoryManager->getElasticAutoSuggestSearchQuery($query, $locale, $limit);
        $response = $this->searchCategoryElastic($searchQuery);

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

    public function searchClosestLocalityInElastic(SearchDTO $params)
    {
        $closestLocality = '';

        if ($params->locationValue->searchCenterLat and $params->locationValue->searchCenterLng) {
            $searchQuery = $this->localityManager->getElasticClosestSearchQuery($params);
            $response = $this->searchLocalityElastic($searchQuery);

            $search = $this->localityManager->getLocalityFromElasticResponse($response);

            $closestLocality = current($search['data']);
        }

        return $closestLocality;
    }

    protected function searchElastic($searchQuery, $documentType)
    {
        try {
            $response = $this->elasticSearchManager->search($searchQuery, $documentType);
        } catch (\Exception $e) {
            $response = [];
        }

        return $response;
    }

    /**
     * @param $searchQuery array
     *
     * @return array
     */
    protected function searchBusinessAdElastic($searchQuery)
    {
        $response = $this->searchElastic($searchQuery, BusinessProfile::ELASTIC_DOCUMENT_TYPE_AD);

        return $response;
    }

    protected function searchBusinessElastic($searchQuery)
    {
        $response = $this->searchElastic($searchQuery, BusinessProfile::ELASTIC_DOCUMENT_TYPE);

        return $response;
    }

    protected function searchCategoryElastic($searchQuery)
    {
        $response = $this->searchElastic($searchQuery, Category::ELASTIC_DOCUMENT_TYPE);

        return $response;
    }

    protected function searchLocalityElastic($searchQuery)
    {
        $response = $this->searchElastic($searchQuery, Locality::ELASTIC_DOCUMENT_TYPE);

        return $response;
    }

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

        // todo use const
        if (!empty($response['aggregations']['ads']['buckets'])) {
            $result = $response['aggregations']['ads']['buckets'];
            $dataIds = [];

            foreach ($result as $item) {
                $dataIds[] = $item['key'];
            }

            // randomize was made on elastic search size

            $dataRaw = $this->getRepository()->findBusinessProfilesByIdsArray($dataIds);

            // todo show rand score

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

    protected function searchBusinessByIdsInArray($data, $id)
    {
        foreach ($data as $item) {
            if ($item->getId() == $id) {
                return $item;
            }
        }

        return false;
    }

    public function createElasticSearchIndex()
    {
        $status = true;
        $mappings = $this->getElasticSearchMappings();

        try {
            $response = $this->elasticSearchManager->createIndex($mappings);
        } catch (\Exception $e) {
            $status = false;
            $message = json_decode($e->getMessage());
            if (!empty($message->error->type) and
                $message->error->type == ElasticSearchManager::INDEX_ALREADY_EXISTS_EXCEPTION
            ) {
                $status = true;
            }
        }

        return $status;
    }

    public function handleElasticSearchIndexRefresh()
    {
        $status = false;

        $deleteStatus = $this->deleteElasticSearchIndex();

        if ($deleteStatus) {
            $createStatus = $this->createElasticSearchIndex();

            if ($createStatus) {
                $this->getRepository()->setUpdatedAllBusinessProfiles();
                $this->categoryManager->setUpdatedAllCategories();
                $this->localityManager->setUpdatedAllLocalities();

                $status = true;
            }
        }

        return $status;
    }

    /**
     * @return array
     */
    protected function getElasticSearchMappings()
    {
        $businessMapping   = $this->getBusinessElasticSearchMapping();
        $businessAdMapping = $this->getBusinessAdElasticSearchMapping();
        $categoryMapping   = $this->categoryManager->getCategoryElasticSearchMapping();
        $localityMapping   = $this->localityManager->getLocalityElasticSearchMapping();

        $mappings = array_merge($businessMapping, $businessAdMapping, $categoryMapping, $localityMapping);

        return $mappings;
    }

    protected function getBusinessElasticSearchMapping($sourceEnabled = true)
    {
        $properties = $this->getBusinessElasticSearchIndexParams();

        $data = [
            BusinessProfile::ELASTIC_DOCUMENT_TYPE => [
                '_source' => [
                    'enabled' => $sourceEnabled,
                ],
                'properties' => $properties,
            ],
        ];

        return $data;
    }

    /**
     * @param $sourceEnabled bool
     *
     * @return array
     */
    protected function getBusinessAdElasticSearchMapping($sourceEnabled = true)
    {
        $properties = $this->getBusinessAdElasticSearchIndexParams();

        $data = [
            BusinessProfile::ELASTIC_DOCUMENT_TYPE_AD => [
                '_source' => [
                    'enabled' => $sourceEnabled,
                ],
                'properties' => $properties,
            ],
        ];

        return $data;
    }

    protected function getCategoryElasticSearchMapping($sourceEnabled = true)
    {
        $properties = $this->categoryManager->getCategoryElasticSearchIndexParams();

        $data = [
            Category::ELASTIC_DOCUMENT_TYPE => [
                '_source' => [
                    'enabled' => $sourceEnabled,
                ],
                'properties' => $properties,
            ],
        ];

        return $data;
    }

    protected function deleteElasticSearchIndex()
    {
        $status = true;

        try {
            $response = $this->elasticSearchManager->deleteIndex();
        } catch (\Exception $e) {
            $status = false;
            $message = json_decode($e->getMessage());

            if (!empty($message->error->type) and
                $message->error->type == ElasticSearchManager::INDEX_NOT_FOUND_EXCEPTION
            ) {
                $status = true;
            }
        }

        return $status;
    }

    /**
     * @param BusinessProfile[] $businessProfiles
     *
     * @return mixed
     */
    public function addBusinessesRawToElasticIndex($businessProfiles)
    {
        // todo remove
        $data = [];
        $response = true;

        foreach ($businessProfiles as $businessProfile) {
            $item = $this->buildBusinessProfileElasticData($businessProfile);

            if ($item) {
                $data[] = $item;
            } else {
                $this->removeBusinessFromElastic($businessProfile->getId());
            }
        }

        if ($data) {
            $response = $this->addElasticBulkBusinessData($data);
        }

        return $response;
    }

    /**
     * @param array $data
     *
     * @return mixed
     */
    public function addBusinessesToElasticIndex($data)
    {
        $response = $this->addElasticBulkItemData($data, BusinessProfile::ELASTIC_DOCUMENT_TYPE);

        return $response;
    }

    /**
     * @param array $data
     *
     * @return mixed
     */
    public function addBusinessesAdToElasticIndex($data)
    {
        $response = $this->addElasticBulkItemData($data, BusinessProfile::ELASTIC_DOCUMENT_TYPE_AD);

        return $response;
    }

    /**
     * @param array $data
     *
     * @return mixed
     */
    public function addCategoriesToElasticIndex($data)
    {
        $response = $this->addElasticBulkItemData($data, Category::ELASTIC_DOCUMENT_TYPE);

        return $response;
    }

    /**
     * @param array $data
     *
     * @return mixed
     */
    public function addLocalitiesToElasticIndex($data)
    {
        $response = $this->addElasticBulkItemData($data, Locality::ELASTIC_DOCUMENT_TYPE);

        return $response;
    }

    protected function addElasticBulkItemData($data, $documentType)
    {
        try {
            $status = $this->elasticSearchManager->addBulkItems($data, $documentType);
        } catch (\Exception $e) {
            $status = false;
            $message = json_decode($e->getMessage());

            //create index if it doesn't exist
            if (!empty($message->error->type) and
                $message->error->type == ElasticSearchManager::INDEX_NOT_FOUND_EXCEPTION
            ) {
                $this->createElasticSearchIndex();
                $status = $this->elasticSearchManager->addBulkItems($data, $documentType);
            }
        }

        return $status;
    }

    protected function removeItemFromElastic($id, $documentType)
    {
        $status = true;

        try {
            $response = $this->elasticSearchManager->deleteItem($id, $documentType);
        } catch (\Exception $e) {
            $status = false;
            $message = json_decode($e->getMessage());

            if (!empty($message->error->type) and
                $message->error->type == ElasticSearchManager::INDEX_NOT_FOUND_EXCEPTION
            ) {
                $status = true;
            }
        }

        return $status;
    }

    public function removeBusinessFromElastic($id)
    {
        $status = $this->removeItemFromElastic($id, BusinessProfile::ELASTIC_DOCUMENT_TYPE);

        return $status;
    }

    /**
     * @param $id int
     *
     * @return bool
     */
    public function removeBusinessAdFromElastic($id)
    {
        $status = $this->removeItemFromElastic($id, BusinessProfile::ELASTIC_DOCUMENT_TYPE_AD);

        return $status;
    }

    public function removeCategoryFromElastic($id)
    {
        $status = $this->removeItemFromElastic($id, Category::ELASTIC_DOCUMENT_TYPE);

        return $status;
    }

    public function removeLocalityFromElastic($id)
    {
        $status = $this->removeItemFromElastic($id, Locality::ELASTIC_DOCUMENT_TYPE);

        return $status;
    }

    protected function getElasticSearchQuery(SearchDTO $params, $locale, $excludeIds = [])
    {
        // see https://jira.oxagile.com/browse/INFT-1197
        $fields = [
            'name_' . strtolower($locale) . '^5',
            'categories_' . strtolower($locale) . '^3',
//            'description_' . strtolower($locale) . '^1',
            'name_' . strtolower($locale) . '.folded^5',
            'categories_' . strtolower($locale) . '.folded^3',
//            'description_' . strtolower($locale) . '.folded^1',
        ];

        $filters = [];

        $sort['subscr_rank'] = [
            'order' => 'desc'
        ];

        $coordinates = $params->getCurrentCoordinates();

        if (SearchDataUtil::ORDER_BY_DISTANCE == $params->getOrderBy()) {
            $sort['_geo_distance'] = [
                'location' => [
                    'lat' => $coordinates['lat'],
                    'lon' => $coordinates['lng'],
                ],
                'unit' => 'mi',
                'order' => 'asc'
            ];
            $sort['_score'] = [
                'order' => 'desc'
            ];
        } else {
            $sort['_score'] = [
                'order' => 'desc'
            ];
            $sort['_geo_distance'] = [
                'location' => [
                    'lat' => $coordinates['lat'],
                    'lon' => $coordinates['lng'],
                ],
                'unit' => 'mi',
                'order' => 'asc'
            ];
        }

        $category = $params->getCategory();

        if ($category) {
            $filters[] = [
                'match' => [
                    'categories_ids' => $category
                ],
            ];
        }

        $neighborhood = $params->getNeighborhood();

        if ($neighborhood) {
            $filters[] = [
                'match' => [
                    'neighborhood_ids' => $neighborhood
                ],
            ];
        }

        $locationQuery  = [];
        $locationFilter = $this->getElasticLocationFilter($params);

        if (!$locationFilter) {
            $locationQuery = $this->getElasticLocationQuery($params);
        }

        $searchQuery = [
            'from' => ($params->page - 1) * $params->limit,
            'size' => $params->limit,
            'track_scores' => true,
            'query' => [
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
                                            'query' => $params->query,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'sort' => [
                $sort
            ],
        ];

        if ($locationQuery) {
            $searchQuery['query']['bool']['must'][] = $locationQuery;
        }

        foreach ($filters as $filter) {
            $searchQuery['query']['bool']['must'][] = $filter;
        }

        if ($locationFilter) {
            $searchQuery['query']['bool']['filter'][] = $locationFilter;
        }

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
     * @param $params SearchDTO
     */
    protected function getElasticSearchQueryAd(SearchDTO $params, $locale)
    {
        // see https://jira.oxagile.com/browse/INFT-1197
        $fields = [
            'name_' . strtolower($locale) . '^5',
            'categories_' . strtolower($locale) . '^3',
//            'description_' . strtolower($locale) . '^1',
            'name_' . strtolower($locale) . '.folded^5',
            'categories_' . strtolower($locale) . '.folded^3',
//            'description_' . strtolower($locale) . '.folded^1',
        ];

        $filters = [];

        $sort['_script'] = [
            'script' => 'Math.random()',
            'type'   => 'number',
            'params' => [],
            'order' => 'asc',
        ];

        $category = $params->getCategory();

        if ($category) {
            $filters[] = [
                'match' => [
                    'categories_ids' => $category
                ],
            ];
        }

        $neighborhood = $params->getNeighborhood();

        if ($neighborhood) {
            $filters[] = [
                'match' => [
                    'neighborhood_ids' => $neighborhood
                ],
            ];
        }

        $locationQuery  = [];
        $locationFilter = $this->getElasticLocationFilter($params);

        if (!$locationFilter) {
            $locationQuery = $this->getElasticLocationQuery($params);
        }

        $searchQuery = [
//            todo - not required
            'from' => 0,
            'size' => 0,
//            'from' => ($params->page - 1) * $params->limit,
//            'size' => $params->limit,
            'track_scores' => true,
            'query' => [
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
                                            'query' => $params->query,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'sort' => [
                $sort
            ],
        ];

        if ($locationQuery) {
            $searchQuery['query']['bool']['must'][] = $locationQuery;
        }

        foreach ($filters as $filter) {
            $searchQuery['query']['bool']['must'][] = $filter;
        }

        if ($locationFilter) {
            $searchQuery['query']['bool']['filter'][] = $locationFilter;
        }

        $searchQuery['aggs'] = [
            // todo use const
            'ads' => [
                'terms' => [
                    'field' => 'parent_id',
                    'order' => [
                        'rand' => 'desc',
                    ],
                    // todo use const or param
                    'size' =>  3,
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

    protected function getCatalogBusinessSearchQuery(SearchDTO $params)
    {
        $filters = [];

        $sort['subscr_rank'] = [
            'order' => 'desc'
        ];

        $coordinates = $params->getCurrentCoordinates();

        if (SearchDataUtil::ORDER_BY_DISTANCE == $params->getOrderBy()) {
            $sort['_geo_distance'] = [
                'location' => [
                    'lat' => $coordinates['lat'],
                    'lon' => $coordinates['lng'],
                ],
                'unit' => 'mi',
                'order' => 'asc'
            ];
            $sort['_score'] = [
                'order' => 'desc'
            ];
        } else {
            $sort['_score'] = [
                'order' => 'desc'
            ];
            $sort['_geo_distance'] = [
                'location' => [
                    'lat' => $coordinates['lat'],
                    'lon' => $coordinates['lng'],
                ],
                'unit' => 'mi',
                'order' => 'asc'
            ];
        }

        $category = $params->getCategory()->getId();

        if ($category) {
            $filters[] = [
                'match' => [
                    'categories_ids' => $category
                ],
            ];
        }

        $locationQuery  = [];
        $locationFilter = $this->getElasticLocationFilter($params);

        if (!$locationFilter) {
            $locationQuery = $this->getElasticLocationQuery($params);
        }

        $searchQuery = [
            'from' => ($params->page - 1) * $params->limit,
            'size' => $params->limit,
            'track_scores' => true,
            'sort' => [
                $sort
            ],
        ];

        if ($locationQuery) {
            $searchQuery['query']['bool']['must'][] = $locationQuery;
        }

        foreach ($filters as $filter) {
            $searchQuery['query']['bool']['must'][] = $filter;
        }

        if ($locationFilter) {
            $searchQuery['query']['bool']['filter'][] = $locationFilter;
        }

        return $searchQuery;
    }

    protected function getElasticLocationQuery(SearchDTO $params)
    {
        $locationQuery = [];

        if (!$params->locationValue->ignoreLocality) {
            $distanceScript = 'doc["location"].arcDistanceInMiles(' . $params->locationValue->lat . ', '
                . $params->locationValue->lng . ') < doc["miles_of_my_business"].value';

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
                                            'script' => $distanceScript,
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
                                    [
                                        'match' => [
                                            'locality_ids' => $localityId,
                                        ],
                                    ],
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

    protected function getElasticAutoSuggestSearchQuery($query, $locale, $limit = false, $offset = 0)
    {
        if (!$limit) {
            $limit = self::AUTO_SUGGEST_MAX_BUSINESSES_COUNT;
        }

        $searchQuery = [
            'from' => $offset,
            'size' => $limit,
            'track_scores' => true,
            'query' => [
                'multi_match' => [
                    'type' => 'most_fields',
                    'query' => $query,
                    'fields' => [
                        'auto_suggest_' . strtolower($locale),
                        'auto_suggest_' . strtolower($locale) . '.folded'
                    ],
                ],
            ],
            'sort' => [
                '_score' => [
                    'order' => 'desc'
                ],
            ],
        ];

        return $searchQuery;
    }

    public function buildBusinessProfileElasticData(BusinessProfile $businessProfile)
    {
        $businessSubscription     = $businessProfile->getSubscription();

        if (!$businessSubscription || $businessSubscription->getStatus() != StatusInterface::STATUS_ACTIVE ||
            !$businessProfile->getIsActive()
        ) {
            return false;
        }

        $enLocale   = strtolower(BusinessProfile::TRANSLATION_LANG_EN);
        $esLocale   = strtolower(BusinessProfile::TRANSLATION_LANG_ES);
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

        $businessProfileNameEn = SearchDataUtil::sanitizeElasticSearchQueryString($businessProfile->getNameEn());
        $businessProfileNameEs = SearchDataUtil::sanitizeElasticSearchQueryString($businessProfile->getNameEs());
        $businessProfileDescEn = SearchDataUtil::sanitizeElasticSearchQueryString($businessProfile->getDescriptionEn());
        $businessProfileDescEs = SearchDataUtil::sanitizeElasticSearchQueryString($businessProfile->getDescriptionEs());

        $autoSuggest[$enLocale][] = $businessProfileNameEn;
        $autoSuggest[$esLocale][] = $businessProfileNameEs;

        if ($businessProfile->getMilesOfMyBusiness()) {
            $milesOfMyBusiness = $businessProfile->getMilesOfMyBusiness();
        } else {
            $milesOfMyBusiness = BusinessProfile::DEFAULT_MILES_FROM_MY_BUSINESS;
        }

        $data = [
            'id'                   => $businessProfile->getId(),
            'name_en'              => $businessProfileNameEn,
            'name_es'              => $businessProfileNameEs,
            'description_en'       => $businessProfileDescEn,
            'description_es'       => $businessProfileDescEs,
            'miles_of_my_business' => $milesOfMyBusiness,
            'categories_en'        => $categories[$enLocale],
            'categories_es'        => $categories[$esLocale],
            'auto_suggest_en'      => $autoSuggest[$enLocale],
            'auto_suggest_es'      => $autoSuggest[$esLocale],
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
     * @param $extraSearch  BusinessProfileExtraSearch
     * @param $data         array
     *
     * @return array
     */
    public function buildBusinessProfileChildElasticData($extraSearch, $data)
    {
        $enLocale   = strtolower(BusinessProfile::TRANSLATION_LANG_EN);
        $esLocale   = strtolower(BusinessProfile::TRANSLATION_LANG_ES);
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

        // todo reconsider ads rank
        $data['subscr_rank'] = 7;
        $data['parent_id'] = $extraSearch->getBusinessProfile()->getId();
        $data['categories_ids'] = $categoryIds;

        return $data;
    }

    protected function getBusinessAdElasticSearchIndexParams()
    {
        $params = $this->getBusinessElasticSearchIndexParams();

        $params['parent_id'] = [
            'type' => 'integer'
        ];

        return $params;
    }

    protected function getBusinessElasticSearchIndexParams()
    {
        $params = [
            'auto_suggest_en' => [
                'type' => 'string',
                'analyzer' => 'autocomplete',
                'search_analyzer' => 'autocomplete_search',
                'fields' => [
                    'folded' => [
                        'type' => 'string',
                        'analyzer' => 'folding',
                    ],
                ],
            ],
            'auto_suggest_es' => [
                'type' => 'string',
                'analyzer' => 'autocomplete',
                'search_analyzer' => 'autocomplete_search',
                'fields' => [
                    'folded' => [
                        'type' => 'string',
                        'analyzer' => 'folding',
                    ],
                ],
            ],
            'name_en' => [
                'type' => 'string',
                'analyzer' => 'autocomplete',
                'search_analyzer' => 'autocomplete_search',
                'fields' => [
                    'folded' => [
                        'type' => 'string',
                        'analyzer' => 'folding',
                    ],
                ],
            ],
            'name_es' => [
                'type' => 'string',
                'analyzer' => 'autocomplete',
                'search_analyzer' => 'autocomplete_search',
                'fields' => [
                    'folded' => [
                        'type' => 'string',
                        'analyzer' => 'folding',
                    ],
                ],
            ],
            'categories_en' => [
                'type' => 'string',
                'analyzer' => 'autocomplete',
                'search_analyzer' => 'autocomplete_search',
                'fields' => [
                    'folded' => [
                        'type' => 'string',
                        'analyzer' => 'folding',
                    ],
                ],
            ],
            'categories_es' => [
                'type' => 'string',
                'analyzer' => 'autocomplete',
                'search_analyzer' => 'autocomplete_search',
                'fields' => [
                    'folded' => [
                        'type' => 'string',
                        'analyzer' => 'folding',
                    ],
                ],
            ],
            'description_en' => [
                'type' => 'string',
                'analyzer' => 'autocomplete',
                'search_analyzer' => 'autocomplete_search',
                'fields' => [
                    'folded' => [
                        'type' => 'string',
                        'analyzer' => 'folding',
                    ],
                ],
            ],
            'description_es' => [
                'type' => 'string',
                'analyzer' => 'autocomplete',
                'search_analyzer' => 'autocomplete_search',
                'fields' => [
                    'folded' => [
                        'type' => 'string',
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
                'type'  => 'string',
                'index' => 'not_analyzed'
            ],
        ];

        return $params;
    }

    public function handleBusinessElasticSync()
    {
        $index = $this->createElasticSearchIndex();

        if ($index) {
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

                if ($item) {
                    $data[] = $item;
                    $this->handleBusinessAdsElasticData($business, $item);
                } else {
                    $this->removeBusinessFromElastic($business->getId());
                }

                $business->setIsUpdated(false);
                $business->setHasImages($this->checkBusinessHasImages($business));

                if (($iElastic % $batchElastic) === 0) {
                    $this->addBusinessesToElasticIndex($data);
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
                $this->addBusinessesToElasticIndex($data);
            }

            $this->em->flush();
        }
    }

    /**
     * @param $business     BusinessProfile
     * @param $parentData   array
     */
    protected function handleBusinessAdsElasticData($business, $parentData)
    {
        $ads = [];

        if (!$business->getExtraSearches()->isEmpty()) {
            foreach ($business->getExtraSearches() as $extraSearch) {
                if ($business->getSubscriptionPlanCode() >= SubscriptionPlanInterface::CODE_PREMIUM_PLATINUM) {
                    $ads[] = $this->buildBusinessProfileChildElasticData($extraSearch, $parentData);
                } else {
                    $this->removeBusinessAdFromElastic($extraSearch->getId());
                }
            }
        }

        if ($ads) {
            $this->addBusinessesAdToElasticIndex($ads);
        }
    }

    public function handleCategoryElasticSync()
    {
        $index = $this->createElasticSearchIndex();

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
                    $this->removeCategoryFromElastic($category->getId());
                }

                $category->setIsUpdated(false);

                if (($countElastic % $batchElastic) === 0) {
                    $this->addCategoriesToElasticIndex($data);
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
                $this->addCategoriesToElasticIndex($data);
            }

            $this->em->flush();
        }
    }

    public function handleLocalityElasticSync()
    {
        $index = $this->createElasticSearchIndex();

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
                    $this->removeLocalityFromElastic($locality->getId());
                }

                $locality->setIsUpdated(false);

                if (($countElastic % $batchElastic) === 0) {
                    $this->addLocalitiesToElasticIndex($data);
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
                $this->addLocalitiesToElasticIndex($data);
            }

            $this->em->flush();
        }
    }

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
}
