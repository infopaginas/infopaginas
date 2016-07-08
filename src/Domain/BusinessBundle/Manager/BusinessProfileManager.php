<?php

namespace Domain\BusinessBundle\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Repository\BusinessProfileRepository;
use FOS\UserBundle\Model\UserInterface;
use Oxa\ManagerArchitectureBundle\Model\Manager\Manager;

use Domain\BusinessBundle\Utils\BusinessProfileUtils;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class BusinessProfileManager extends Manager
{
    /**
     * @var CategoryManager
     */
    protected $categoryManager;

    /** @var UserInterface */
    private $currentUser = null;

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
    public function __construct(EntityManager $entityManager, CategoryManager $categoryManager, TokenStorageInterface $tokenStorage)
    {
        $this->em = $entityManager;

        $this->categoryManager = $categoryManager;

        $this->currentUser = $tokenStorage->getToken()->getUser();
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
            $business->setLocale($locale);
            $this->getEntityManager()->refresh($business);
        }

        return $business;
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

        $businessProfile->setLocale($locale);
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
   public function publish(BusinessProfile $businessProfile)
   {
       $oldProfile = $businessProfile->getActualBusinessProfile();
       $oldProfile->setActualBusinessProfile($businessProfile);
       $oldProfile->setIsActive(false);

       $this->commit($oldProfile);

       $businessProfile->setActualBusinessProfile(null);
       $businessProfile->setIsActive(true);

       $this->commit($businessProfile);
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

    private function drop(BusinessProfile $businessProfile)
    {
        $this->getEntityManager()->remove($businessProfile);
        $this->getEntityManager()->flush();
    }

    /**
     * @return EntityManager
     */
    private function getEntityManager() : EntityManager
    {
        return $this->em;
    }
}
