<?php

namespace Domain\BusinessBundle\Manager;

use Doctrine\ORM\EntityManager;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Form\Type\BusinessProfileFormType;
use Domain\BusinessBundle\Model\SubscriptionPlanInterface;
use Domain\BusinessBundle\Repository\BusinessGalleryRepository;
use Domain\BusinessBundle\Repository\BusinessReviewRepository;
use Domain\BusinessBundle\Util\BusinessProfile\BusinessProfilesComparator;
use FOS\UserBundle\Model\UserInterface;
use Gedmo\Translatable\TranslatableListener;
use JMS\Serializer\SerializerBuilder;
use Oxa\ManagerArchitectureBundle\Model\Manager\Manager;
use Oxa\WistiaBundle\Manager\WistiaMediaManager;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Oxa\GeolocationBundle\Model\Geolocation\LocationValueObject;
use Domain\BusinessBundle\Util\BusinessProfileUtil;
use Domain\SearchBundle\Model\DataType\SearchDTO;

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

    /** @var BusinessGalleryManager */
    private $businessGalleryManager;

    /** @var WistiaMediaManager */
    private $wistiaMediaManager;

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
     * @param BusinessGalleryManager $businessGalleryManager
     * @param WistiaMediaManager $wistiaMediaManager
     */
    public function __construct(
        EntityManager $entityManager,
        CategoryManager $categoryManager,
        TokenStorageInterface $tokenStorage,
        TranslatableListener $translatableListener,
        FormFactory $formFactory,
        BusinessGalleryManager $businessGalleryManager,
        WistiaMediaManager $wistiaMediaManager
    ) {
        $this->em = $entityManager;

        $this->categoryManager = $categoryManager;

        if ($tokenStorage->getToken() !== null) {
            $this->currentUser = $tokenStorage->getToken()->getUser();
        }

        $this->translatableListener = $translatableListener;

        $this->formFactory = $formFactory;

        $this->businessGalleryManager = $businessGalleryManager;

        $this->wistiaMediaManager = $wistiaMediaManager;
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

    public function searchAutosuggestByPhraseAndLocation(string $phrase, string $location)
    {
        $categories       = $this->categoryManager->searchAutosuggestByName($phrase);
        $businessProfiles = $this->getRepository()->searchAutosuggestWithBuilder($phrase);

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
        return $this->getRepository()->search($searchParams);
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
            'locked' => false,
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
            'locked' => false,
            'actualBusinessProfile' => null,
        ]);

        return $businessProfile;
    }

    /**
     * @param BusinessProfile $businessProfile
     * @return BusinessProfile
     */
    public function cloneProfile(BusinessProfile $businessProfile) : BusinessProfile
    {
        $clonedProfile = clone $businessProfile;
        $clonedProfile->setUid($businessProfile->getUid());

        $this->commit($clonedProfile);

        return $clonedProfile;
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
    public function lock(BusinessProfile $businessProfile)
    {
        $businessProfile->setLocked(true);
        $this->commit($businessProfile);
    }

    /**
     * @param BusinessProfile $businessProfile
     */
    public function unlock(BusinessProfile $businessProfile)
    {
        $businessProfile->setLocked(false);
    }

    /**
     * @param BusinessProfile $businessProfile
     */
    public function restore(BusinessProfile $businessProfile)
    {
        $actualBusinessProfile = $businessProfile->getActualBusinessProfile();
        $this->unlock($actualBusinessProfile);

        $this->commit($actualBusinessProfile);
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
     * @param string $locale
     */
    public function publish(BusinessProfile $businessProfile, $locale = 'en_US')
    {
        $oldProfile = $businessProfile->getActualBusinessProfile();

        $businessProfile->setActualBusinessProfile(null);
        $businessProfile->setIsActive(true);

        //backup object translations
        if ($locale === BusinessProfile::DEFAULT_LOCALE) {
            foreach ($oldProfile->getTranslations() as $translation) {
                $businessProfile->addTranslation($translation);
            }
        }

        // ¯ \ _ (ツ) _ / ¯
        $oldProfileSubscription = $oldProfile->getSubscription();
        $newProfileSubscription = $businessProfile->getSubscription();

        $oldProfileSubscription->setBusinessProfile($businessProfile);
        $newProfileSubscription->setBusinessProfile($oldProfile);

        $this->getEntityManager()->persist($oldProfileSubscription);
        $this->getEntityManager()->persist($newProfileSubscription);

        $this->getBusinessGalleryManager()->setupBusinessProfileLogo($businessProfile);

        $discount = $oldProfile->getDiscount();

        if ($discount !== null) {
            $discount->setBusinessProfile($businessProfile);
            $this->getEntityManager()->persist($discount);

            $this->getEntityManager()->refresh($oldProfile);
        }

        $this->getEntityManager()->persist($businessProfile);
        $this->getEntityManager()->flush();

        $this->drop($oldProfile);
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
     * @param string $locale
     * @return string
     */
    public function getSerializedProfileChanges(BusinessProfile $businessProfile, $locale = 'en_US')
    {
        $actualBusinessProfile = $businessProfile->getActualBusinessProfile() ?
            $businessProfile->getActualBusinessProfile() : new BusinessProfile();

        if ($locale !== BusinessProfile::DEFAULT_LOCALE && !empty($locale)) {
            $businessProfile->setLocale($locale);
            $actualBusinessProfile->setLocale($locale);

            $this->getEntityManager()->refresh($businessProfile);
            $this->getEntityManager()->refresh($actualBusinessProfile);
        }

        $changes = BusinessProfilesComparator::compare(
            $this->getBusinessProfileAsForm($businessProfile),
            $this->getBusinessProfileAsForm($actualBusinessProfile)
        );

        $serializer = SerializerBuilder::create()->build();

        return $serializer->serialize($changes, 'json');
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

    public function getLastReviewForBusinessProfile(BusinessProfile $businessProfile)
    {
        $lastReview = $this->getBusinessProfileReviewsRepository()->findBusinessProfileLastReview($businessProfile);
        return $lastReview;
    }

    /**
     * @param BusinessProfile $businessProfile
     */
    public function drop(BusinessProfile $businessProfile)
    {
        $this->getEntityManager()->remove($businessProfile);
        $this->getEntityManager()->flush();
    }

    public function countSearchResults(SearchDTO $searchParams)
    {
        return $this->getRepository()->countSearchResults($searchParams);
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
        return $this->getEntityManager()->getRepository(BusinessGalleryRepository::SLUG);
    }

    /**
     * @return WistiaMediaManager
     */
    private function getWistiaMediaManager() : WistiaMediaManager
    {
        return $this->wistiaMediaManager;
    }

    /**
     * @return BusinessGalleryManager
     */
    private function getBusinessGalleryManager() : BusinessGalleryManager
    {
        return $this->businessGalleryManager;
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
        return $this->getEntityManager()->getRepository(BusinessReviewRepository::SLUG);
    }

    /**
     * @return EntityManager
     */
    protected function getEntityManager() : EntityManager
    {
        return $this->em;
    }
}
