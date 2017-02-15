<?php

namespace Domain\BusinessBundle\Manager;

use AntiMattr\GoogleBundle\Analytics;
use AntiMattr\GoogleBundle\Analytics\Impression;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\BusinessProfilePhone;
use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Entity\ChangeSet;
use Domain\BusinessBundle\Entity\ChangeSetEntry;
use Domain\BusinessBundle\Entity\Locality;
use Domain\BusinessBundle\Entity\Media\BusinessGallery;
use Domain\BusinessBundle\Entity\Review\BusinessReview;
use Domain\BusinessBundle\Entity\Translation\BusinessProfileTranslation;
use Domain\BusinessBundle\Form\Type\BusinessProfileFormType;
use Domain\BusinessBundle\Model\StatusInterface;
use Domain\BusinessBundle\Model\SubscriptionPlanInterface;
use Domain\BusinessBundle\Repository\BusinessGalleryRepository;
use Domain\BusinessBundle\Repository\BusinessReviewRepository;
use Domain\BusinessBundle\Util\ChangeSetCalculator;
use Domain\BusinessBundle\Util\Task\PhoneChangeSetUtil;
use Domain\BusinessBundle\Util\SlugUtil;
use Domain\BusinessBundle\Util\Task\RelationChangeSetUtil;
use Domain\BusinessBundle\Util\Task\TranslationChangeSetUtil;
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
    public function __construct(ContainerInterface $container) {
        $this->container = $container;

        $this->em = $container->get('doctrine.orm.entity_manager');

        $this->categoryManager = $container->get('domain_business.manager.category');

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

        if ($locale !== BusinessProfile::DEFAULT_LOCALE) {
            $businessProfile->setLocale($locale);
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

    public function publish(BusinessProfile $businessProfile, ChangeSet $changeSet, $locale = 'en')
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        /** @var ChangeSetEntry $change */
        foreach ($changeSet->getEntries() as $change) {
            switch ($change->getAction()) {
                case ChangeSetCalculator::PROPERTY_ADD:
                    $accessor->setValue($businessProfile, $change->getFieldName(), $change->getNewValue());
                    break;
                case ChangeSetCalculator::PROPERTY_REMOVE:
                case ChangeSetCalculator::LOGO_REMOVE:
                case ChangeSetCalculator::BACKGROUND_REMOVE:
                    $accessor->setValue($businessProfile, $change->getFieldName(), null);
                    break;
                case ChangeSetCalculator::PROPERTY_CHANGE:
                    if (!$change->getClassName()) {
                        if (strstr($change->getFieldName(), 'isSet') && empty($change->getNewValue())) {
                            $newValue = false;
                        } else {
                            $newValue = $change->getNewValue();
                        }
                        $accessor->setValue($businessProfile, $change->getFieldName(), $newValue);
                    } else {
                        if ($change->getClassName() === BusinessProfilePhone::class) {
                            $collection = PhoneChangeSetUtil::getPhonesCollectionsFromChangeSet(
                                $change,
                                $businessProfile,
                                $this->getEntityManager()
                            );

                            $accessor->setValue($businessProfile, $change->getFieldName(), $collection);
                        } elseif ($change->getFieldName() === BusinessProfile::BUSINESS_PROFILE_FIELD_COUNTRY or
                            $change->getFieldName() === BusinessProfile::BUSINESS_PROFILE_FIELD_CATALOG_LOCALITY) {

                            $item = RelationChangeSetUtil::getRelationEntityFromChangeSet(
                                $change,
                                $this->getEntityManager()
                            );

                            $accessor->setValue($businessProfile, $change->getFieldName(), $item);

                        } else {
                            $ids = array_map(function($element) {
                                return $element->id;
                            }, json_decode($change->getNewValue()));

                            $collection = $this->getEntitiesByIds($change->getClassName(), $ids);
                            $accessor->setValue($businessProfile, $change->getFieldName(), $collection);
                        }
                    }
                    break;
                case ChangeSetCalculator::LOGO_ADD:
                case ChangeSetCalculator::LOGO_UPDATE:
                case ChangeSetCalculator::BACKGROUND_ADD:
                case ChangeSetCalculator::BACKGROUND_UPDATE:
                    $data  = json_decode($change->getNewValue());
                    $media = $this->getEntityManager()->getRepository(Media::class)->find($data->id);

                    if ($media) {
                        $accessor->setValue($businessProfile, $change->getFieldName(), $media);
                    }

                    break;
                case ChangeSetCalculator::IMAGE_ADD:
                    $data = json_decode($change->getNewValue());
                    $media = $this->getEntityManager()->getRepository(Media::class)->find($data->media);

                    if ($media) {
                        $businessProfile->addImage(BusinessGallery::createFromChangeSet($data, $media));

                        $this->getSonataMediaManager()->save($media, false);
                    }

                    break;
                case ChangeSetCalculator::IMAGE_REMOVE:
                    $data = json_decode($change->getOldValue());
                    $gallery = $this->getEntityManager()->getRepository(BusinessGallery::class)->find($data->id);
                    if ($gallery) {
                        $businessProfile->removeImage($gallery);
                        $this->getEntityManager()->remove($gallery);
                    }
                    break;
                case ChangeSetCalculator::PROPERTY_IMAGE_PROPERTY_UPDATE:
                    // update BusinessGallery properties - type and description
                    $new = json_decode($change->getNewValue());

                    $dataNew = current($new);
                    $itemNew = json_decode($dataNew->value);
                    $gallery = $this->getEntityManager()->getRepository(BusinessGallery::class)->find($dataNew->id);

                    if (!$gallery) {
                        break;
                    }

                    $gallery->setDescription($itemNew->description);

                    if (isset($data->type)) {
                        $gallery->setType($itemNew->type);
                    }

                    break;
                case ChangeSetCalculator::VIDEO_ADD:
                    $data = json_decode($change->getNewValue());
                    $video = $this->getEntityManager()->getRepository(VideoMedia::class)->find($data->id);

                    if ($video) {
                        if (!empty($data->title)) {
                            $video->setTitle($data->title);
                        }

                        if (!empty($data->description)) {
                            $video->setDescription($data->description);
                        }

                        if ($video->getYoutubeSupport()) {
                            $video->setYoutubeAction(VideoMedia::YOUTUBE_ACTION_ADD);
                        }

                        $businessProfile->setVideo($video);
                    }
                    break;
                case ChangeSetCalculator::VIDEO_REMOVE:
                    $manager = $this->getVideoManager()->removeMedia($businessProfile->getVideo()->getId());
                    $businessProfile->setVideo(null);
                    break;
                case ChangeSetCalculator::VIDEO_UPDATE:
                    $data = json_decode($change->getNewValue());
                    //if video was replaced
                    if (!empty($change->getOldValue())) {
                        $video = $this->getEntityManager()->getRepository(VideoMedia::class)->find($data->id);
                        $manager = $this->getVideoManager()->removeMedia($businessProfile->getVideo()->getId());

                        if ($video) {
                            if (!empty($data->title)) {
                                $video->setTitle($data->title);
                            }

                            if (!empty($data->description)) {
                                $video->setDescription($data->description);
                            }

                            if ($video->getYoutubeSupport()) {
                                $video->setYoutubeAction(VideoMedia::YOUTUBE_ACTION_ADD);
                            }

                            $businessProfile->setVideo($video);
                        }
                    }
                    break;
                case ChangeSetCalculator::VIDEO_PROPERTY_UPDATE:
                    $data = json_decode($change->getNewValue());

                    $video = $this->getEntityManager()->getRepository(VideoMedia::class)->find($data->id);

                    if ($video) {
                        if (!empty($data->title)) {
                            $video->setTitle($data->title);
                        }

                        if (!empty($data->description)) {
                            $video->setDescription($data->description);
                        }

                        if ($video->getYoutubeSupport() and !$video->getYoutubeAction() and $video->getYoutubeId()) {
                            $video->setYoutubeAction(VideoMedia::YOUTUBE_ACTION_UPDATE);
                        }

                        $businessProfile->setVideo($video);
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
                case ChangeSetCalculator::PROPERTY_CHANGE:
                    if ($change->getClassName() === BusinessProfileTranslation::class) {
                        $collection = TranslationChangeSetUtil::getTranslationCollectionsFromChangeSet(
                            $change,
                            $businessProfile,
                            $this->getEntityManager()
                        );

                        $accessor->setValue($businessProfile, $change->getFieldName(), $collection);
                    }
                break;
            }
        }

        $this->getEntityManager()->flush();
    }

    public function getTaskMediaLink(ChangeSetEntry $change, $value)
    {
        $url = '';

        switch ($change->getAction()) {
            case ChangeSetCalculator::LOGO_ADD:
            case ChangeSetCalculator::LOGO_UPDATE:
            case ChangeSetCalculator::LOGO_REMOVE:
            case ChangeSetCalculator::BACKGROUND_ADD:
            case ChangeSetCalculator::BACKGROUND_UPDATE:
            case ChangeSetCalculator::BACKGROUND_REMOVE:
                $data  = json_decode($value);
                $media = $this->getEntityManager()->getRepository(Media::class)->find($data->id);

                if ($media) {
                    $url = $this->getMediaPublicUrl($media, 'reference');
                }

                break;
            case ChangeSetCalculator::IMAGE_ADD:
            case ChangeSetCalculator::IMAGE_REMOVE:
                $data = json_decode($value);
                $media = $this->getEntityManager()->getRepository(Media::class)->find($data->media);

                if ($media) {
                    $url = $this->getMediaPublicUrl($media, 'reference');
                }

                break;
            case ChangeSetCalculator::VIDEO_ADD:
            case ChangeSetCalculator::VIDEO_REMOVE:
            case ChangeSetCalculator::VIDEO_UPDATE:
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
        foreach ($searchResultsDTO->resultSet as $key => $item)
        {
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
        $code = $businessProfile->getSubscription()->getSubscriptionPlan()->getCode();
        return $code >= SubscriptionPlanInterface::CODE_PRIORITY;
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
        return new DCDataDTO([], '', [$profile->getCategory()->getName()], $profile->getSlug());
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

            $openingHours = $businessProfile->getWorkingHours();
            if ($openingHours) {
                $schemaItem['openingHours'] = $openingHours;
            }

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
    )
    {
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
        if (SearchDataUtil::ORDER_BY_RELEVANCE == $searchParams->getOrderBy()) {
            $randomize = true;
        } else {
            $randomize = false;
        }

        $searchQuery = $this->getElasticSearchQuery($searchParams, $locale);
        $response = $this->searchBusinessElastic($searchQuery);
        $search = $this->getBusinessDataFromElasticResponse($response, $randomize);

        $search['data'] = array_map(function ($item) use ($searchParams) {
            $distance = GeolocationUtils::getDistanceForPoint(
                $searchParams->locationValue->lat,
                $searchParams->locationValue->lng,
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
            $name = [];

            $parent1 = $item->getParent();

            if ($parent1) {
                $parent2 = $parent1->getParent();

                if ($parent2) {
                    $name[] = $parent2->getName();
                }

                $name[] = $parent1->getName();
            }

            $name[] = $item->getName();

            $data = implode(CategoryManager::AUTO_SUGGEST_SEPARATOR, $name);

            return [
                'type' => CategoryManager::AUTO_COMPLETE_TYPE,
                'name' => $data,
                'data' => $data,
            ];
        }, $search['data']);

        return $search['data'];
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
                    $score = 0;
                    $plan = 1;

                    foreach ($result as $business) {
                        if ($business['_id'] == $id) {
                            $plan = $business['sort'][0];
                            $score = number_format($business['sort'][1], ElasticSearchManager::ROTATION_RANK_PRECISION);
                            break;
                        }
                    }

                    $item->setScore($score);
                    $item->setPlan($plan);
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
                $status = true;
            }
        }

        return $status;
    }

    protected function getElasticSearchMappings()
    {
        $businessMapping = $this->getBusinessElasticSearchMapping();
        $categoryMapping = $this->categoryManager->getCategoryElasticSearchMapping();

        $mappings = array_merge($businessMapping, $categoryMapping);

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

        if (SearchDataUtil::ORDER_BY_DISTANCE == $params->getOrderBy()) {
            $sort['_geo_distance'] = [
                'location' => [
                    'lat' => $params->locationValue->lat,
                    'lon' => $params->locationValue->lng,
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
                    'lat' => $params->locationValue->lat,
                    'lon' => $params->locationValue->lng,
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

        $locationQuery = [];

        if (!$params->locationValue->ignoreLocality) {
            $locationQuery = [
                'bool' => [
                    'minimum_should_match' => 1,
                    'should' => [
                        [
                            'bool' => [
                                'must' => [
                                    [
                                        'script' => [
                                            'script' => 'doc["location"].arcDistanceInMiles(' . $params->locationValue->lat . ', ' . $params->locationValue->lng . ') < doc["miles_of_my_business"].value'
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
                                            'locality_ids' => $params->locationValue->locality ? $params->locationValue->locality->getId() : 0,
                                        ],
                                    ],
                                    [
                                        'term' => [
                                            'service_areas_type' => BusinessProfile::SERVICE_AREAS_LOCALITY_CHOICE_VALUE,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ];
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

        return $searchQuery;
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

        $data = [
            'id'                   => $businessProfile->getId(),
            'name_en'              => $businessProfile->getNameEn(),
            'name_es'              => $businessProfile->getNameEs(),
            'description_en'       => $businessProfile->getDescriptionEn(),
            'description_es'       => $businessProfile->getDescriptionEs(),
            'miles_of_my_business' => $businessProfile->getMilesOfMyBusiness() ?: BusinessProfile::DEFAULT_MILES_FROM_MY_BUSINESS,
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
