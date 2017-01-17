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
use Domain\BusinessBundle\Model\SubscriptionPlanInterface;
use Domain\BusinessBundle\Repository\BusinessGalleryRepository;
use Domain\BusinessBundle\Repository\BusinessReviewRepository;
use Domain\BusinessBundle\Util\ChangeSetCalculator;
use Domain\BusinessBundle\Util\Task\PhoneChangeSetUtil;
use Domain\BusinessBundle\Util\SlugUtil;
use Domain\BusinessBundle\Util\Task\RelationChangeSetUtil;
use Domain\BusinessBundle\Util\Task\TranslationChangeSetUtil;
use FOS\UserBundle\Model\UserInterface;
use Gedmo\Translatable\TranslatableListener;
use Oxa\ElasticSearchBundle\Manager\ElasticSearchManager;
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
        $categories = $this->categoryManager->searchAutosuggestByName($query, $locale);
        $businessProfiles = $this->getRepository()->searchAutosuggestWithBuilder($query, ucwords($locale));

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
        $searchResultsData = $this->getRepository()->search($searchParams, $locale);
        $searchResultsData = array_map(function ($item) {
            return $item[0]->setDistance($item['distance']);
        }, $searchResultsData);

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

    public function preSaveProfile(BusinessProfile $businessProfile)
    {
        $logoEmpty = $backgroundEmpty = true;

        foreach ($businessProfile->getImages() as $gallery) {
            if ($gallery->getType() == OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_LOGO) {
                $media = $gallery->getMedia();

                $businessProfile->setLogo($media);
                $businessProfile->removeImage($gallery);
                $logoEmpty = false;
            } elseif ($gallery->getType() == OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_BACKGROUND) {
                $media = $gallery->getMedia();

                $businessProfile->setBackground($media);
                $businessProfile->removeImage($gallery);
                $backgroundEmpty = false;
            }
        }

        if ($logoEmpty && $businessProfile->getLogo()) {
            $businessProfile->setLogo();
        }

        if ($backgroundEmpty && $businessProfile->getBackground()) {
            $businessProfile->setBackground();
        }

        return $businessProfile;
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
                case ChangeSetCalculator::PROPERTY_IMAGE_ADD:
                    $data   = json_decode($change->getNewValue());

                    $media = $this->getEntityManager()->getRepository(Media::class)->find($data->id);
                    $media = $this->setMediaContentAndProvider($media, $data->context);

                    if ($data->context == OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_LOGO) {
                        $businessProfile->setLogo($media);
                    }elseif ($data->context == OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_BACKGROUND) {
                        $businessProfile->setBackground($media);
                    }

                    $this->getEntityManager()->persist($media);
                    break;
                case ChangeSetCalculator::PROPERTY_IMAGE_REMOVE:
                    $data   = json_decode($change->getOldValue());

                    if ($data->context == OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_LOGO) {
                        $businessProfile->setLogo();
                    } elseif ($data->context == OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_BACKGROUND) {
                        $businessProfile->setBackground();
                    }
                    break;
                case ChangeSetCalculator::PROPERTY_IMAGE_UPDATE:
                    $data   = json_decode($change->getNewValue());

                    $media = $this->getEntityManager()->getRepository(Media::class)->find($data->id);
                    $media = $this->setMediaContentAndProvider($media, $data->context);

                    if ($data->context == OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_LOGO) {
                        $businessProfile->setLogo($media);
                    }elseif ($data->context == OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_BACKGROUND) {
                        $businessProfile->setBackground($media);
                    }

                    $this->getEntityManager()->persist($media);
                    break;
                case ChangeSetCalculator::IMAGE_ADD:
                    $data = json_decode($change->getNewValue());
                    $media = $this->getEntityManager()->getRepository(Media::class)->find($data->media);
                    if ($media->getContext() == OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_LOGO) {
                        $media = $this->setMediaContentAndProvider($media, $data->type);
                        $businessProfile->addImage(BusinessGallery::createFromChangeSet($data, $media));
                    } elseif ($media->getContext() == OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_BACKGROUND) {
                        $media = $this->setMediaContentAndProvider($media, $data->type);
                        $businessProfile->addImage(BusinessGallery::createFromChangeSet($data, $media));
                    } else {
                        $businessProfile->addImage(BusinessGallery::createFromChangeSet($data, $media));
                    }

                    $this->getSonataMediaManager()->save($media, false);

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
                    $old = json_decode($change->getOldValue());

                    $dataNew = current($new);
                    $dataOld = current($old);

                    $itemNew = json_decode($dataNew->value);
                    $itemOld = json_decode($dataOld->value);

                    $gallery = $this->getEntityManager()->getRepository(BusinessGallery::class)->find($dataNew->id);

                    if (!$gallery) {
                        break;
                    }

                    // update gallery description
                    if (isset($itemNew->description)) {
                        $gallery->setDescription($itemNew->description);
                    }

                    // update gallery type
                    if (isset($itemNew->type)) {
                        $gallery->setType($itemNew->type);

                        $media = $gallery->getMedia();

                        //convert image logo and background to simple image
                        $context = '';
                        if ($itemOld->type != $itemNew->type) {
                            if ($itemOld->type === OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_LOGO) {
                                $context = OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_LOGO;
                                $businessProfile->setLogo(null);
                            } elseif ($itemOld->type === OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_BACKGROUND) {
                                $context = OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_BACKGROUND;
                                $businessProfile->setBackground(null);
                            }
                        }

                        // add former logo to image collection
                        if ($context == OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_LOGO
                            && $media->getContext() == OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_LOGO
                        ) {
                            $media = $this->setMediaContentAndProvider(
                                $media,
                                OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_IMAGES
                            );
                            $gallery->setMedia($media);
                            $businessProfile->addImage($gallery);
                        }

                        // add former background to image collection
                        if ($context == OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_BACKGROUND
                            && $media->getContext() == OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_BACKGROUND
                        ) {
                            $media = $this->setMediaContentAndProvider(
                                $media,
                                OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_IMAGES
                            );
                            $gallery->setMedia($media);
                            $businessProfile->addImage($gallery);
                        }
                    }

                    break;
                case ChangeSetCalculator::IMAGE_UPDATE:
                    $data = json_decode($change->getNewValue());
                    $gallery = $this->getEntityManager()->getRepository(BusinessGallery::class)->find($data->id);

                    if (!$gallery) {
                        break;
                    }

                    if (isset($data->description)) {
                        $gallery->setDescription($data->description[1]);
                    }

                    if (isset($data->isPrimary)) {
                        $gallery->setIsPrimary($data->isPrimary[1]);
                    }

                    if (isset($data->type)) {
                        $gallery->setType($data->type[1]);

                        $media = $gallery->getMedia();

                        $context = '';
                        if ($data->type[0] != $data->type[1]) {
                            if ($data->type[0] === OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_LOGO) {
                                $context = OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_LOGO;
                                $businessProfile->setLogo(null);
                            } elseif ($data->type[0] === OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_BACKGROUND) {
                                $context = OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_BACKGROUND;
                                $businessProfile->setBackground(null);
                            }
                        }

                        if ($context == OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_LOGO 
                                && $media->getContext() == OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_LOGO
                                ) {
                            $media = $this->setMediaContentAndProvider($media, $data->context);
                            $gallery->setMedia($media);
                            $businessProfile->addImage($gallery);
                        }
                        if ($context == OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_BACKGROUND
                                && $media->getContext() == OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_BACKGROUND
                                ) {
                            $media = $this->setMediaContentAndProvider($media, $data->context);
                            $gallery->setMedia($media);
                            $businessProfile->addImage($gallery);
                        }
                    }

                    $this->getEntityManager()->persist($gallery);
                    break;
                case ChangeSetCalculator::VIDEO_ADD:
                    $data = json_decode($change->getNewValue());
                    $video = $this->getEntityManager()->getRepository(VideoMedia::class)->find($data->id);
                    $businessProfile->setVideo($video);
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
    public function countSearchResults(SearchDTO $searchParams, string $locale)
    {
        return $this->getRepository()->countSearchResults($searchParams, $locale);
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

    protected function makeLogoFromPhoto(Media $media)
    {
        $media = clone $media;
        $media->setContext(OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_LOGO);
        $this->getSonataMediaManager()->save($media, false);

        return $media;
    }

    protected function makePhotoFromLogo(Media $media)
    {
        $media = clone $media;
        $media->setContext(OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_IMAGES);
        $this->getSonataMediaManager()->save($media, false);

        return $media;
    }

    protected function makeBackgroundFromPhoto(Media $media)
    {
        $media = clone $media;
        $media->setContext(OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_BACKGROUND);
        $this->getSonataMediaManager()->save($media, false);

        return $media;
    }

    protected function makePhotoFromBackground(Media $media)
    {
        $media = clone $media;
        $media->setContext(OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_IMAGES);
        $this->getSonataMediaManager()->save($media, false);

        return $media;
    }

    protected function setMediaContentAndProvider(Media $media, $newContext)
    {
        $provider = $this->container->get('sonata.media.pool')->getProvider($media->getProviderName());

        $filepath = sprintf('%s/%s', $provider->generatePath($media), $media->getProviderReference());
        $path = $provider->getFilesystem()->getAdapter()->getDirectory() . DIRECTORY_SEPARATOR . $filepath;

        if (!$provider->getFilesystem()->has($filepath)) {
            return $media;
        }

        $media->setBinaryContent($path);
        $media->setProviderReference($media->getPreviousProviderReference());
        $media->setContext($newContext);

        $provider->transform($media);

        return $media;
    }

    public function addLogoAndBackgroundToGallery($businessProfile)
    {
        $galleryManager = $this->getBusinessGalleryManager();

        $media = $businessProfile->getLogo();
        if ($media) {
            $media->setContext(OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_LOGO);
            $galleryManager->addNewItemToBusinessProfileGallery($businessProfile, $media);
        }

        $media = $businessProfile->getBackground();
        if ($media) {
            $media->setContext(OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_BACKGROUND);
            $galleryManager->addNewItemToBusinessProfileGallery($businessProfile, $media);
        }

        return $businessProfile;
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

    public function createElasticSearchIndex()
    {
        $status = true;
        $properties = $this->getElasticSearchIndexParams();

        try {
            $response = $this->elasticSearchManager->createIndex($properties);
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
        $response = $this->addElasticBulkBusinessData($data);

        return $response;
    }

    protected function addElasticBulkBusinessData($data)
    {
        try {
            $status = $this->elasticSearchManager->addBulkItems($data);
        } catch (\Exception $e) {
            $status = false;
            $message = json_decode($e->getMessage());

            //create index if it doesn't exist
            if (!empty($message->error->type) and
                $message->error->type == ElasticSearchManager::INDEX_NOT_FOUND_EXCEPTION
            ) {
                $this->createElasticSearchIndex();
                $status = $this->elasticSearchManager->addBulkItems($data);
            }
        }

        return $status;
    }

    public function removeBusinessFromElastic($id)
    {
        $status = true;

        try {
            $response = $this->elasticSearchManager->deleteItem($id);
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

    protected function getElasticSearchQuery($params, $pageStart, $itemsLimit)
    {
        //todo params set
        $fields = [
            'name_en^5',
            'categories_en^3',
            'description_en^1',
        ];

        if (isset($params['lang'])) {
            $fields = [
                'name_es^5',
                'categories_es^3',
                'description_es^1',
            ];
        }

        $searchQuery = [
            'from' => $pageStart,
            'size' => $itemsLimit,
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
                                            'query' => $params['query']
                                        ]
                                    ],
                                ],
                            ],
                        ],
                        [
                            'bool' => [
                                'minimum_should_match' => 1,
                                'should' => [
                                    [
                                        'script' => [
                                            'script' => 'doc["location"].arcDistanceInMiles('.$params['lat'].', '.$params['lon'].') < doc["miles_of_my_business"].value'
                                        ]
                                    ],
                                    [
                                        'match' => [
                                            'locality_id' => [
                                                'query' => $params['locality'],
                                            ]
                                        ]
                                    ],
                                ]
                            ]
                        ],
                    ],
                ],
            ],
            'sort' => [
                [
                    'subscr_rank'   => [
                        'order' => 'desc'
                    ],
                    '_score' => [
                        'order' => 'desc'
                    ],
                    '_geo_distance' => [
                        'location' => [
                            'lat' => $params['lat'],
                            'lon' => $params['lon'],
                        ],
                        'unit' => 'mi',
                        'order' => 'asc'
                    ]
                ]
            ],
        ];

        return $searchQuery;
    }

    public function buildBusinessProfileElasticData(BusinessProfile $businessProfile)
    {
        $categories = [];
        $locales    = [
            strtolower(BusinessProfile::TRANSLATION_LANG_EN),
            strtolower(BusinessProfile::TRANSLATION_LANG_ES),
        ];

        foreach ($locales as $locale) {
            $categories[$locale] = [];
        }

        foreach ($businessProfile->getCategories() as $category) {
            foreach ($category->getTranslations() as $translation) {
                if (in_array($translation->getLocale(), $locales)) {
                    $categories[$translation->getLocale()][] = $translation->getContent();
                }
            }
        }

        $businessSubscription     = $businessProfile->getSubscription();
        $businessSubscriptionPlan = $businessProfile->getSubscriptionPlan();

        $data = [
            'id'                   => $businessProfile->getId(),
            'name_en'              => $businessProfile->getNameEn(),
            'name_es'              => $businessProfile->getNameEs(),
            'description_en'       => $businessProfile->getDescriptionEn(),
            'description_es'       => $businessProfile->getDescriptionEs(),
            'miles_of_my_business' => $businessProfile->getMilesOfMyBusiness() ?: 0,
            'categories_en'        => $categories[strtolower(BusinessProfile::TRANSLATION_LANG_EN)],
            'categories_es'        => $categories[strtolower(BusinessProfile::TRANSLATION_LANG_ES)],
            'is_active'            => $businessProfile->getIsActive(),
            'location'             => [
                'lat' => $businessProfile->getLatitude(),
                'lon' => $businessProfile->getLongitude(),
            ],
            'service_areas_type'   => $businessProfile->getServiceAreasType(),
            'locality_id'          => $businessProfile->getCatalogLocality()->getId(),
            'subscr_status'        => $businessSubscription ? $businessSubscription->getStatus() : 0,
            'subscr_rank'          => $businessSubscriptionPlan ? $businessSubscriptionPlan->getRank() : 0,
        ];

        return $data;
    }

    protected function getElasticSearchIndexParams()
    {
        $params = [
            'location' => [
                'type' => 'geo_point'
            ],
            'location_id' => [
                'type' => 'integer'
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
}
