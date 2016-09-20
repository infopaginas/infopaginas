<?php

namespace Domain\BusinessBundle\Manager;

use AntiMattr\GoogleBundle\Analytics;
use AntiMattr\GoogleBundle\Analytics\Impression;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\ChangeSet;
use Domain\BusinessBundle\Entity\ChangeSetEntry;
use Domain\BusinessBundle\Entity\Media\BusinessGallery;
use Domain\BusinessBundle\Entity\Review\BusinessReview;
use Domain\BusinessBundle\Form\Type\BusinessProfileFormType;
use Domain\BusinessBundle\Model\SubscriptionPlanInterface;
use Domain\BusinessBundle\Repository\BusinessGalleryRepository;
use Domain\BusinessBundle\Repository\BusinessReviewRepository;
use Domain\BusinessBundle\Util\ChangeSetCalculator;
use FOS\UserBundle\Model\UserInterface;
use Gedmo\Translatable\TranslatableListener;
use Oxa\ManagerArchitectureBundle\Model\Manager\Manager;
use Oxa\Sonata\MediaBundle\Entity\Media;
use Oxa\Sonata\UserBundle\Entity\User;
use Oxa\WistiaBundle\Entity\WistiaMedia;
use Oxa\WistiaBundle\Manager\WistiaMediaManager;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Oxa\GeolocationBundle\Model\Geolocation\LocationValueObject;
use Domain\BusinessBundle\Util\BusinessProfileUtil;
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

    /** @var WistiaMediaManager */
    private $wistiaMediaManager;

    /** @var Analytics $analytics */
    private $analytics;

    /**
     * Manager constructor.
     * Accepts only entityManager as main dependency.
     * Regargless hole container, need to keep it clear and work only with needed dependency
     *
     * @param EntityManager $entityManager
     * @param CategoryManager $categoryManager
     * @param TokenStorageInterface $tokenStorage
     * @param TranslatableListener $translatableListener
     * @param FormFactory $formFactory
     * @param WistiaMediaManager $wistiaMediaManager
     * @param Analytics $analytics
     */
    public function __construct(
        EntityManager $entityManager,
        CategoryManager $categoryManager,
        TokenStorageInterface $tokenStorage,
        TranslatableListener $translatableListener,
        FormFactory $formFactory,
        WistiaMediaManager $wistiaMediaManager,
        Analytics $analytics
    ) {
        $this->em = $entityManager;

        $this->categoryManager = $categoryManager;

        if ($tokenStorage->getToken() !== null) {
            $this->currentUser = $tokenStorage->getToken()->getUser();
        }

        $this->currentUser = $this->em->getRepository(User::class)->find(1);

        $this->translatableListener = $translatableListener;

        $this->formFactory = $formFactory;

        $this->wistiaMediaManager = $wistiaMediaManager;

        $this->analytics = $analytics;
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

    public function searchAutosuggestByPhraseAndLocation(SearchDTO $searchParams)
    {
        $categories       = $this->categoryManager->searchAutosuggestByName($searchParams->query);
        $businessProfiles = $this->getRepository()->searchAutosuggestWithBuilder($searchParams);

        $result = array_merge($categories, $businessProfiles);
        return $result;
    }

    public function searchWithMapByPhraseAndLocation(string $phrase, string $location)
    {
        if (empty($location)) {
            $location = self::DEFAULT_LOCALE_NAME;
        }

        // TODO Move to filtering functionality
        $phrase = preg_replace("/[^a-zA-Z0-9\s]+/", "", $phrase);
        return $this->getRepository()->searchWithQueryBuilder($phrase, $location);
    }

    public function getLocationMarkersFromProfileData(array $profilesList)
    {
        return BusinessProfileUtil::filterLocationMarkers($profilesList);
    }

    public function search(SearchDTO $searchParams)
    {
        $searchResultsData = $this->getRepository()->search($searchParams);
        $searchResultsData = array_map(function ($item) {
            return $item[0]->setDistance($item['distance']);
        }, $searchResultsData);

        return $searchResultsData;
    }

    public function searchNeighborhood(SearchDTO $searchParams)
    {
        return $this->getRepository()->searchNeighborhood($searchParams);
    }

    /**
     * @param int $id
     * @param string $locale
     * @return null|object
     */
    public function find(int $id, string $locale = 'en_US')
    {
        $business = $this->getRepository()->find($id);

        if ($locale !== 'en_US') {
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
        $businessProfile = $this->getRepository()->findOneBy([
            'slug' => $slug,
            'isActive' => true,
        ]);

        return $businessProfile;
    }

    /**
     * @return BusinessProfile
     */
    public function createProfile() : BusinessProfile
    {
        $profile = new BusinessProfile();
        $profile->setIsActive(false);

        return $profile;
    }

    /**
     * @param BusinessProfile $businessProfile
     * @param string $locale
     */
    public function saveProfile(BusinessProfile $businessProfile, string $locale = 'en_US')
    {
        if (!$businessProfile->getId()) {
            $businessProfile->setIsActive(false);
        }

        if ($locale !== BusinessProfile::DEFAULT_LOCALE) {
            $businessProfile->setLocale($locale);
        }

        $businessProfile->setUser($this->currentUser);

        foreach ($businessProfile->getImages() as $image) {
            $clonedImage = clone $image;
            $clonedImage->setBusinessProfile($businessProfile);
            $this->getEntityManager()->persist($clonedImage);
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



    public function publish(BusinessProfile $businessProfile, ChangeSet $changeSet, $locale = 'en_US')
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
                        $accessor->setValue($businessProfile, $change->getFieldName(), $change->getNewValue());
                    } else {
                        $ids = array_map(function($element) {
                            return $element->id;
                        }, json_decode($change->getNewValue()));

                        $collection = $this->getEntitiesByIds($change->getClassName(), $ids);
                        $accessor->setValue($businessProfile, $change->getFieldName(), $collection);
                    }
                    break;
                case ChangeSetCalculator::IMAGE_ADD:
                    $data = json_decode($change->getNewValue());
                    $media = $this->getEntityManager()->getRepository(Media::class)->find($data->media);
                    $businessProfile->addImage(BusinessGallery::createFromChangeSet($data, $media));
                    break;
                case ChangeSetCalculator::IMAGE_REMOVE:
                    $data = json_decode($change->getNewValue());
                    $gallery = $this->getEntityManager()->getRepository(BusinessGallery::class)->find($data->id);
                    $businessProfile->removeImage($gallery);
                    break;
                case ChangeSetCalculator::IMAGE_UPDATE:
                    $data = json_decode($change->getNewValue());
                    $gallery = $this->getEntityManager()->getRepository(BusinessGallery::class)->find($data->id);
                    if (isset($data->description)) {
                        $gallery->setDescription($data->description[1]);
                    }
                    if (isset($data->isPrimary)) {
                        $gallery->setIsPrimary($data->isPrimary[1]);
                    }
                    if (isset($data->type)) {
                        $gallery->setType($data->type[1]);
                    }
                    $this->getEntityManager()->persist($gallery);
                    break;
                case ChangeSetCalculator::VIDEO_ADD:
                    $data = json_decode($change->getNewValue());
                    $video = $this->getEntityManager()->getRepository(WistiaMedia::class)->find($data->id);
                    $businessProfile->setVideo($video);
                    break;
                case ChangeSetCalculator::VIDEO_REMOVE:
                    $businessProfile->setVideo(null);
                    break;
                case ChangeSetCalculator::VIDEO_UPDATE:
                    $data = json_decode($change->getNewValue());
                    //if video was replaced
                    if (!empty($change->getOldValue())) {
                        $video = $this->getEntityManager()->getRepository(WistiaMedia::class)->find($data->id);
                        $businessProfile->setVideo($video);
                    } else {
                        if (isset($data->description)) {
                            $businessProfile->getVideo()->setDescription($data->description[1]);
                        }
                        if (isset($data->name)) {
                            $businessProfile->getVideo()->setName($data->name[1]);
                        }
                    }
                    break;
            }
        }

        $this->getEntityManager()->persist($businessProfile);
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
     * @return BusinessProfile
     */
    public function checkBusinessProfileVideo(BusinessProfile $businessProfile)
    {
        if ($businessProfile->getVideo()) {
            $video = $this->getWistiaMediaManager()->updateNameAndDescriptionByWistiaID($businessProfile->getVideo());
            $businessProfile->setVideo($video);
        }

        return $businessProfile;
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
     * @return mixed
     */
    public function countSearchResults(SearchDTO $searchParams)
    {
        return $this->getRepository()->countSearchResults($searchParams);
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
     * @return BusinessGalleryRepository
     */
    private function getBusinessGalleryRepository() : BusinessGalleryRepository
    {
        return $this->getEntityManager()->getRepository(BusinessGallery::class);
    }

    /**
     * @return WistiaMediaManager
     */
    private function getWistiaMediaManager() : WistiaMediaManager
    {
        return $this->wistiaMediaManager;
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
        return new DCDataDTO(array(), '', array(), $profile->getSlug());
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
}
