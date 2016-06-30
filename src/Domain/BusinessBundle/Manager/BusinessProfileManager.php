<?php

namespace Domain\BusinessBundle\Manager;

use Doctrine\ORM\EntityManager;
use Oxa\ManagerArchitectureBundle\Model\Manager\Manager;

use Domain\BusinessBundle\Utils\BusinessProfileUtils;

class BusinessProfileManager extends Manager
{
    /**
     * @var CategoryManager
     */
    protected $categoryManager;

    /**
     * Manager constructor.
     * Accepts only entityManager as main dependency.
     * Regargless hole container, need to keep it clear and work only with needed dependency
     *
     * @access public
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager, CategoryManager $categoryManager)
    {
        $this->em = $entityManager;
        $this->categoryManager = $categoryManager;
    }

    public function searchByPhraseAndLocation(string $phrase, string $location)
    {
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
        return $this->getRepository()->search($phrase, $location);
    }

    public function getLocationMarkersFromProfileData(array $profilesList)
    {
        return BusinessProfileUtils::filterLocationMarkers($profilesList);
    }
}
