<?php

namespace Domain\SearchBundle\Model\Manager;

use Domain\BusinessBundle\Entity\Category;
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

    protected $businessProfileManager;
    protected $categoriesManager;
    protected $geolocationManager;
    protected $localityManager;

    public function __construct(
        EntityManager $em,
        Config $configService,
        BusinessProfileManager $businessProfileManager,
        CategoryManager $categoryManager,
        GeolocationManager $geolocationManager,
        LocalityManager $localityManager
    ) {
        parent::__construct($em);

        $this->configService            = $configService;
        $this->businessProfileManager  = $businessProfileManager;
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

    public function search(SearchDTO $searchParams, string $locale) : SearchResultsDTO
    {
        $results = $this->businessProfileManager->search($searchParams, $locale);

        if (!$results) {
            // todo - change logic to 40 miles
            $results  = [];
        }

        if ($results) {
            $totalResults   = $this->businessProfileManager->countSearchResults($searchParams, $locale);
            $categories     = $this->categoriesManager->getCategoriesByProfiles($results);

            // get by current locality

            $neighborhoods  = $this->localityManager->getLocalityNeighborhoods($searchParams->locationValue->locality);

            $pagesCount     = ceil($totalResults/$searchParams->limit);
        } else {
            $totalResults = 0;
            $categories = [];
            $neighborhoods = [];
            $pagesCount = 0;
        }

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

    public function searchCatalog(SearchDTO $searchParams, $locale) : SearchResultsDTO
    {
        $results = $this->businessProfileManager->searchCatalog($searchParams, $locale);

        if ($results) {
            $categories    = [];
            $totalResults  = $this->businessProfileManager->countCatalogSearchResults($searchParams, $locale);
            $neighborhoods = $this->localityManager->getLocalityNeighborhoods($searchParams->locationValue->locality);
            $pagesCount    = ceil($totalResults/$searchParams->limit);
        } else {
            $totalResults  = 0;
            $categories    = [];
            $neighborhoods = [];
            $pagesCount    = 0;
        }

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

    public function getSearchDTO(Request $request)
    {
        $location = $this->geolocationManager->buildLocationValueFromRequest($request);

        if (!$location) {
            return null;
        }

        $query      = preg_replace("/[^a-zA-Z0-9\s]+/", "", SearchDataUtil::getQueryFromRequest($request));
        $page       = SearchDataUtil::getPageFromRequest($request);

        $limit      = (int) $this->configService->getSetting(ConfigInterface::DEFAULT_RESULTS_PAGE_SIZE)->getValue();
        $searchDTO  = SearchDataUtil::buildRequestDTO($query, $location, $page, $limit);

        $category = SearchDataUtil::getCategoryFromRequest($request);

        if ($category) {
            $searchDTO->setCategory($category);
        }

        $neighborhood = SearchDataUtil::getNeighborhoodFromRequest($request);

        if ($neighborhood) {
            $searchDTO->setNeighborhood($neighborhood);
        }

        $orderBy = SearchDataUtil::getOrderByFromRequest($request);

        if ($orderBy) {
            $searchDTO->setOrderBy($orderBy);
        }

        return $searchDTO;
    }

    public function getSearchCatalogDTO($request, $locality, $category, $subcategory)
    {
        $location = $this->geolocationManager->buildLocationValueFromRequest($request);

        if (!$location) {
            return null;
        }

        $query     = preg_replace("/[^a-zA-Z0-9\s]+/", "", SearchDataUtil::getQueryFromRequest($request));
        $page      = SearchDataUtil::getPageFromRequest($request);

        $limit     = (int) $this->configService->getSetting(ConfigInterface::DEFAULT_RESULTS_PAGE_SIZE)->getValue();
        $searchDTO = SearchDataUtil::buildRequestDTO($query, $location, $page, $limit);

        if ($category instanceof Category) {
            $searchDTO->setCategory($category);
        }

        if ($subcategory instanceof Category) {
            $searchDTO->setSubcategory($subcategory);
        }

        if ($locality) {
            $searchDTO->setCatalogLocality($locality);
        }

        $neighborhood = SearchDataUtil::getNeighborhoodFromRequest($request);

        if ($neighborhood) {
            $searchDTO->setNeighborhood($neighborhood);
        }

        $orderBy = SearchDataUtil::getOrderByFromRequest($request);

        if ($orderBy) {
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

    public function searchCatalogLocality($localitySlug)
    {
        $locality = null;

        if ($localitySlug) {
            $locality = $this->localityManager->getLocalityBySlug($localitySlug);
        }

        return $locality;
    }

    public function searchCatalogCategory($categorySlug)
    {
        $category = null;

        if ($categorySlug) {
            $category = $this->categoriesManager->getCategoryBySlug($categorySlug);
        }

        return $category;
    }

    public function searchSubcategoryByCategory($category, $locale)
    {
        $category = $this->categoriesManager->searchSubcategoryByCategory($category, $locale);

        return $category;
    }
}
