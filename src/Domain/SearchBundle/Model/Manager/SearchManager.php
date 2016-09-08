<?php

namespace Domain\SearchBundle\Model\Manager;

use Oxa\ManagerArchitectureBundle\Model\Manager\Manager;
use Oxa\GeolocationBundle\Model\Geolocation\LocationValueObject;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\BusinessBundle\Manager\CategoryManager;
use Domain\BusinessBundle\Manager\LocalityManager;
use Oxa\GeolocationBundle\Model\Geolocation\GeolocationManager;
use Oxa\ConfigBundle\Service\Config;
use Oxa\ConfigBundle\Model\ConfigInterface;

use Domain\SearchBundle\Util\SearchDataUtil;
use Domain\BusinessBundle\Util\BusinessProfileUtil;

use Domain\SearchBundle\Model\DataType\SearchDTO;
use Domain\SearchBundle\Model\DataType\SearchResultsDTO;
use Domain\SearchBundle\Model\DataType\DCDataDTO;

class SearchManager extends Manager
{
    protected $configService;

    protected $businessProfilehManager;
    protected $categoriesManager;
    protected $geolocationManager;
    protected $localityManager;

    public function __construct(
        EntityManager $em,
        Config $configService,
        BusinessProfileManager $businessProfilehManager,
        CategoryManager $categoryManager,
        GeolocationManager $geolocationManager,
        LocalityManager $localityManager
    ) {
        parent::__construct($em);

        $this->configService            = $configService;
        $this->businessProfilehManager  = $businessProfilehManager;
        $this->categoriesManager        = $categoryManager;
        $this->geolocationManager       = $geolocationManager;
        $this->localityManager          = $localityManager;
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

        $totalResults       = $this->businessProfilehManager->countSearchResults($searchParams);
        $categories         = $this->categoriesManager->getCategoriesByProfiles($results);

        $neighborhoodsData  = $this->localityManager
            ->getNeighborhoodLocationsByLocalityName($searchParams->locationValue->name);

        $neighborhoods      = SearchDataUtil::extractNeigborhoods($neighborhoodsData);

        $pagesCount          = ceil($totalResults/$searchParams->limit);

        $response = SearchDataUtil::buildResponceDTO(
            $results,
            $totalResults,
            $searchParams->page,
            $pagesCount,
            $categories,
            $neighborhoods
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

        if ($orderBy = SearchDataUtil::getOrderByFromRequest($request)) {
            $searchDTO->setOrderBy($orderBy);
        }

        return $searchDTO;
    }

    public function getDoubleClickData(SearchDTO $searchDTO) : DCDataDTO
    {
        return new DCDataDTO(
            explode(' ', $searchDTO->query),
            $searchDTO->locationValue->name,
            $searchDTO->getCategory()
        );
    }
}
