<?php

namespace Domain\SearchBundle\Model\Manager;

use Oxa\ManagerArchitectureBundle\Model\Manager\Manager;
use Oxa\GeolocationBundle\Model\Geolocation\LocationValueObject;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\BusinessBundle\Manager\CategoryManager;
use Oxa\GeolocationBundle\Model\Geolocation\GeolocationManager;
use Oxa\ConfigBundle\Service\Config;
use Oxa\ConfigBundle\Model\ConfigInterface;

use Domain\SearchBundle\Util\SearchDataUtil;
use Domain\BusinessBundle\Util\BusinessProfileUtil;

use Domain\SearchBundle\Model\DataType\SearchDTO;
use Domain\SearchBundle\Model\DataType\SearchResultsDTO;

class SearchManager extends Manager
{
    protected $configService;

    protected $businessProfilehManager;
    protected $categoriesManager;
    protected $geolocationManager;

    public function __construct(
        EntityManager $em,
        Config $configService,
        BusinessProfileManager $businessProfilehManager,
        CategoryManager $categoryManager,
        GeolocationManager $geolocationManager
    ) {
        parent::__construct($em);

        $this->configService            = $configService;
        $this->businessProfilehManager  = $businessProfilehManager;
        $this->categoriesManager        = $categoryManager;
        $this->geolocationManager       = $geolocationManager;
    }

    public function getAutocompleteDataByPhrase(string $phrase)
    {

    }

    public function getAutocompleteDataByLocation(LocationValueObject $location)
    {

    }

    public function getAutocompleteDataByPhraseAndLocation(string $phrase, LocationValueObject $location)
    {

    }

    public function searchByPhrase(string $phrase)
    {

    }

    public function searchByLocation(LocationValueObject $location)
    {

    }

    public function searchByPhraseAndLocation(string $phrase, string $location)
    {
        $this->getRepository()->getSearchQuery($phrase, $location);
    }

    public function search(SearchDTO $searchParams) : SearchResultsDTO
    {
        $results      = $this->businessProfilehManager->search($searchParams);

        if (empty($results)) {
            $results  = $this->businessProfilehManager->searchNeighborhood($searchParams);
        }

        //$totalResults       = $this->businessProfilehManager->countSearchResults($searchParams);
        $businessProfiles   = BusinessProfileUtil::extractBusinessProfiles($results);
        $categories         = $this->categoriesManager->getCategoriesByProfiles($businessProfiles);

        $totalResultsCount   = count($results);
        $pagesCount          = $totalResultsCount/$searchParams->limit;

        $response = SearchDataUtil::buildResponceDTO(
            $businessProfiles,
            $totalResultsCount,
            $searchParams->page,
            $pagesCount,
            $categories,
            array()
        );

        return $response;
    }

    public function getSearchDTO(Request $request) : SearchDTO
    {
        $query      = SearchDataUtil::getQueryFromRequest($request);
        $page       = SearchDataUtil::getPageFromRequest($request);

        $location   = $this->geolocationManager->buildLocationValueFromRequest($request);
        $limit      = (int) $this->configService->getSetting(ConfigInterface::DEFAULT_RESULTS_PAGE_SIZE)->getValue();

        $searchDTO  = SearchDataUtil::buildRequestDTO($query, $location, $page, $limit);

        if ($category = SearchDataUtil::getCategoryFromRequest($request)) {
            $searchDTO->setCategory($category);
        }

        if ($neighborhood = SearchDataUtil::getNeighborhoodFromRequest($request)) {
            $searchDTO->setNeighborhood($neighborhood);
        }

        return $searchDTO;
    }
}
