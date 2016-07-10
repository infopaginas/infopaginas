<?php

namespace Domain\BusinessBundle\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Form\Type\FreeBusinessProfileFormType;
use Domain\BusinessBundle\Repository\BusinessProfileRepository;
use Domain\BusinessBundle\Util\BusinessProfile\BusinessProfilesComparator;
use FOS\UserBundle\Model\UserInterface;
use Gedmo\Translatable\TranslatableListener;
use JMS\Serializer\SerializerBuilder;
use Oxa\ManagerArchitectureBundle\Model\Manager\Manager;

use Domain\BusinessBundle\Utils\BusinessProfileUtils;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class BusinessProfileManager extends Manager
{
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

    /**
     * Manager constructor.
     * Accepts only entityManager as main dependency.
     * Regargless hole container, need to keep it clear and work only with needed dependency
     *
     * @access public
     * @param EntityManager $entityManager
     * @param CategoryManager $categoryManager
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        EntityManager $entityManager,
        CategoryManager $categoryManager,
        TokenStorageInterface $tokenStorage,
        TranslatableListener $translatableListener,
        FormFactory $formFactory
    ) {
        $this->em = $entityManager;

        $this->categoryManager = $categoryManager;

        if ($tokenStorage->getToken() !== null) {
            $this->currentUser = $tokenStorage->getToken()->getUser();
        }

        $this->translatableListener = $translatableListener;

        $this->formFactory = $formFactory;
    }

    public function searchByPhraseAndLocation(string $phrase, string $location)
    {
        if (empty($location)) {
            // TODO Move magic string this to config
            $location = "San Juan";
        }

        // TODO Move to filtering functionality
        $phrase = preg_replace("/[^a-zA-Z0-9\s]+/", "", $phrase);
        return $this->getRepository()->search($phrase, $location);
    }

    public function searchAutosuggestByPhraseAndLocation(string $phrase, string $location)
    {
        $categories       = $this->categoryManager->searchAutosuggestByName($phrase);
        $businessProfiles = $this->getRepository()->searchAutosuggest($phrase, $location);

        $result = array_merge($categories, $businessProfiles);
        return $result;
    }

    public function searchWithMapByPhraseAndLocation(string $phrase, string $location)
    {
        if (empty($location)) {
            // TODO Move magic string this to config
            $location = "San Juan";
        }

        // TODO Move to filtering functionality
        $phrase = preg_replace("/[^a-zA-Z0-9\s]+/", "", $phrase);
        return $this->getRepository()->search($phrase, $location);
    }

    public function getLocationMarkersFromProfileData(array $profilesList)
    {
        return BusinessProfileUtils::filterLocationMarkers($profilesList);
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

    public function cloneProfile(BusinessProfile $businessProfile) : BusinessProfile
    {
        $clonedProfile = clone $businessProfile;

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
        //todo: move to model
        if (!$businessProfile->getId()) {
            $businessProfile->setIsActive(false);
        }

        if ($locale !== BusinessProfile::DEFAULT_LOCALE) {
            $businessProfile->setLocale($locale);
        }

        $businessProfile->setUser($this->currentUser);

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

       $this->commit($businessProfile);
       $this->drop($oldProfile);
   }

    /**
     * @param BusinessProfile $businessProfile
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getBusinessProfileAsForm(BusinessProfile $businessProfile)
    {
        $form = $this->formFactory->createBuilder(FreeBusinessProfileFormType::class)->getForm();
        $form->setData($businessProfile);

        return $form;
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
     */
    public function drop(BusinessProfile $businessProfile)
    {
        $this->getEntityManager()->remove($businessProfile);
        $this->getEntityManager()->flush();
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

    private function getTranslatableListener() : TranslatableListener
    {
        return $this->translatableListener;
    }

    /**
     * @return EntityManager
     */
    private function getEntityManager() : EntityManager
    {
        return $this->em;
    }
}
