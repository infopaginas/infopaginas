<?php

namespace Domain\BusinessBundle\Manager;

use AntiMattr\GoogleBundle\Analytics;
use AntiMattr\GoogleBundle\Analytics\Impression;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\BusinessProfilePhone;
use Domain\BusinessBundle\Entity\BusinessProfileWorkingHour;
use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Entity\ChangeSet;
use Domain\BusinessBundle\Entity\ChangeSetEntry;
use Domain\BusinessBundle\Entity\Locality;
use Domain\BusinessBundle\Entity\Media\BusinessGallery;
use Domain\BusinessBundle\Entity\Review\BusinessReview;
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
use Domain\SearchBundle\Util\SearchDataUtil;
use FOS\UserBundle\Model\UserInterface;
use Gedmo\Translatable\TranslatableListener;
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

    /** @var UserInterface */
    private $currentUser = null;

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

        $tokenStorage = $container->get('security.token_storage');

        if ($tokenStorage->getToken() !== null) {
            $this->currentUser = $tokenStorage->getToken()->getUser();
        }

        $this->currentUser = $this->em->getRepository(User::class)->find(1);

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

    public function searchCatalog(SearchDTO $searchParams, string $locale)
    {
        $searchResultsData = $this->getRepository()->searchCatalog($searchParams, $locale);

        $searchResultsData = array_map(function ($item) {
            return $item[0]->setDistance($item['distance']);
        }, $searchResultsData);

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

        $this->getTranslatableListener()->setTranslatableLocale($locale);
        $this->getTranslatableListener()->setTranslationFallback('');

        $this->getEntityManager()->refresh($business);

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
        $customSlug = SlugUtil::convertSlug($slug);

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
                    $accessor->setValue($businessProfile, $change->getFieldName(), $change->getNewValue());
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
     * @param BusinessProfile $businessProfile
     * @return array
     */
    public function getBusinessProfileAdvertisementImages(BusinessProfile $businessProfile)
    {
        $subscriptionPlanCode = $businessProfile->getSubscription()->getSubscriptionPlan()->getCode();

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
        $subscriptionPlanCode = $businessProfile->getSubscription()->getSubscriptionPlan()->getCode();

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

        $code = $businessProfile->getSubscription()->getSubscriptionPlan()->getCode();

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

    public function getSubcategories($categoryId, $businessProfileId, $request)
    {
        $data = [];
        $checkedSubcategoryIds = [];

        if ($businessProfileId) {
            /* @var BusinessProfile $businessProfile */
            $businessProfile       = $this->getRepository()->find($businessProfileId);
            $checkedSubcategoryIds = $this->getBusinessProfileSubcategoryIds($businessProfile, $request);
        }

        $subcategories = $this->getSubcategoriesForCategory($categoryId, $request);

        foreach ($subcategories as $key => $subcategory) {
            if ($request['locale']) {
                $name = $subcategory->{'getSearchText' . ucfirst($request['locale'])}();
            } else {
                $name = $subcategory->getName();
            }

            $data[$key] = [
                'id'       => $subcategory->getId(),
                'name'     => $name,
                'selected' => false,
            ];

            if (in_array($subcategory->getId(), $checkedSubcategoryIds)) {
                $data[$key]['selected'] = true;
            }
        }

        return $data;
    }

    public function getSubcategoriesForCategory($categoryId, $request)
    {
        if ($request['level'] == Category::CATEGORY_LEVEL_2) {
            $categoryIds = [$categoryId];
        } else {
            $categoryIds = $request['subcategories'];
        }

        $subcategories = $this->getEntityManager()->getRepository('DomainBusinessBundle:Category')
            ->getAvailableSubCategories($categoryIds, $request['level']);

        return $subcategories;
    }

    public function getCategoriesByIds($ids)
    {
        $subcategories = $this->getEntityManager()->getRepository('DomainBusinessBundle:Category')
            ->getAvailableCategoriesByIds($ids);

        return $subcategories;
    }

    /**
     * @param BusinessProfile $businessProfile
     * @param array           $request
     *
     * @return array
     */
    public function getBusinessProfileSubcategoryIds(BusinessProfile $businessProfile, $request)
    {
        $data = [];
        $subcategories = $businessProfile->getSubcategories($request['level']);

        foreach ($subcategories as $subcategory) {
            $data[] = $subcategory->getId();
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
                    $schemaItem['description'] = $description;
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

        return json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
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

        return json_encode([$schemaItem], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
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
        $openingHoursCollection = $businessProfile->getCollectionWorkingHours();

        if (!$openingHoursCollection->isEmpty()) {
            $dailyHours = DayOfWeekModel::getBusinessProfileWorkingHoursList($businessProfile);
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
                            if ($item->getOpenAllTime()) {
                                // open all time
                                $openTime  = DayOfWeekModel::SCHEMA_ORG_OPEN_ALL_DAY_OPEN_TIME;
                                $closeTime = DayOfWeekModel::SCHEMA_ORG_OPEN_ALL_DAY_CLOSE_TIME;
                            } else {
                                $openTime  = $item->getTimeStart()->format(DayOfWeekModel::SCHEMA_ORG_OPEN_TIME_FORMAT);
                                $closeTime = $item->getTimeEnd()->format(DayOfWeekModel::SCHEMA_ORG_OPEN_TIME_FORMAT);
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

    protected function searchBusinessInElastic(SearchDTO $searchParams, $locale)
    {
        //randomize feature works only for relevance sorting ("Best match")
        $randomize = $searchParams->randomizeAllowed();

        $searchQuery = $this->getElasticSearchQuery($searchParams, $locale);
        $response = $this->searchBusinessElastic($searchQuery);
        $search = $this->getBusinessDataFromElasticResponse($response, $randomize);

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

    protected function searchCategoryAutoSuggestInElastic($query, $locale)
    {
        $searchQuery = $this->categoryManager->getElasticAutoSuggestSearchQuery($query, $locale);
        $response = $this->searchCategoryElastic($searchQuery);

        $search = $this->categoryManager->getCategoryFromElasticResponse($response);

        $search['data'] = array_map(function ($item) {
            return [
                'type' => CategoryManager::AUTO_COMPLETE_TYPE,
                'name' => $item->getName(),
                'data' => $item->getName(),
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

    protected function getElasticSearchMappings()
    {
        $businessMapping = $this->getBusinessElasticSearchMapping();
        $categoryMapping = $this->categoryManager->getCategoryElasticSearchMapping();
        $localityMapping = $this->localityManager->getLocalityElasticSearchMapping();

        $mappings = array_merge($businessMapping, $categoryMapping, $localityMapping);

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

    protected function getElasticSearchQuery(SearchDTO $params, $locale)
    {
        $fields = [
            'name_' . strtolower($locale) . '^5',
            'categories_' . strtolower($locale) . '^3',
            'description_' . strtolower($locale) . '^1',
            'name_' . strtolower($locale) . '.folded^5',
            'categories_' . strtolower($locale) . '.folded^3',
            'description_' . strtolower($locale) . '.folded^1',
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

        $category = $params->getCategory1();

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
        $businessSubscriptionPlan = $businessProfile->getSubscriptionPlan();

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
            $categories[$enLocale][] = $category->getTranslation('name', $enLocale);
            $categories[$esLocale][] = $category->getTranslation('name', $esLocale);
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

        $autoSuggest[$enLocale][] = $businessProfile->getNameEn();
        $autoSuggest[$esLocale][] = $businessProfile->getNameEs();

        if ($businessProfile->getMilesOfMyBusiness()) {
            $milesOfMyBusiness = $businessProfile->getMilesOfMyBusiness();
        } else {
            $milesOfMyBusiness = BusinessProfile::DEFAULT_MILES_FROM_MY_BUSINESS;
        }

        $data = [
            'id'                   => $businessProfile->getId(),
            'name_en'              => $businessProfile->getNameEn(),
            'name_es'              => $businessProfile->getNameEs(),
            'description_en'       => $businessProfile->getDescriptionEn(),
            'description_es'       => $businessProfile->getDescriptionEs(),
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
            'subscr_rank'          => $businessSubscriptionPlan ? $businessSubscriptionPlan->getRank() : 0,
            'neighborhood_ids'     => $neighborhoodIds,
            'categories_ids'       => $categoryIds,
        ];

        return $data;
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
                } else {
                    $this->removeBusinessFromElastic($business->getId());
                }

                $business->setIsUpdated(false);

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
            $rank = (string)number_format($item['rank'], ElasticSearchManager::ROTATION_RANK_PRECISION);

            $raw[$plan][$rank][] = $item['id'];
        }

        $result = [];

        foreach ($raw as $code => $items) {
            foreach ($items as $rank => $businesses) {
                if ($code != SubscriptionPlanInterface::CODE_FREE) {
                    shuffle($businesses);
                }

                $result = array_merge($result, $businesses);
            }
        }

        return $result;
    }
}
