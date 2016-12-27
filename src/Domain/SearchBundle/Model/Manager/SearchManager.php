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
use Domain\BusinessBundle\Entity\Locality;

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
            $pagesCount     = ceil($totalResults/$searchParams->limit);
        } else {
            $totalResults = 0;
            $categories = [];
            $pagesCount = 0;
        }

        $neighborhoods = $this->localityManager->getLocalityNeighborhoods($searchParams->locationValue->locality);

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
            $pagesCount    = ceil($totalResults/$searchParams->limit);
        } else {
            $totalResults  = 0;
            $categories    = [];
            $pagesCount    = 0;
        }

        $neighborhoods = $this->localityManager->getLocalityNeighborhoods($searchParams->locationValue->locality);

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
        $query    = SearchDataUtil::getQueryFromRequest($request);

        if (!$location or !$query) {
            return null;
        }

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

    public function getSearchCatalogDTO($request, $locality, $category, $category2, $category3)
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

        if ($category2 instanceof Category) {
            $searchDTO->setCategory2($category2);
        }

        if ($category3 instanceof Category) {
            $searchDTO->setCategory3($category3);
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

    public function searchSubcategoryByCategory($category, $level, $locale)
    {
        $category = $this->categoriesManager->searchSubcategoryByCategory($category, $level, $locale);

        return $category;
    }

    public function getCategoryParents($category)
    {
        $categories = $this->categoriesManager->getCategoryParents($category);

        return $categories;
    }

    public function getCategoryParentsCatalogPath($category)
    {
        $data = [
            'categorySlug'    => null,
            'categorySlug2'   => null,
            'categorySlug3'   => null,
        ];
        $categories = $this->getCategoryParents($category);

        foreach ($categories as $item) {
            if ($item->getLvl() == Category::CATEGORY_LEVEL_1) {
                $data['categorySlug'] = $item->getSlug();
            } else {
                $data['categorySlug' . $item->getLvl()] = $item->getSlug();
            }
        }

        return $data;
    }

    /**
     * @param Locality[] $localities
     * @param Category[] $categories
     * @param Category[] $categories2
     * @param Category[] $categories3
     *
     * @return array();
     */
    public function sortCatalogItems($localities, $categories = [], $categories2 = [], $categories3 = [])
    {
        if ($categories3) {
            $data = $this->sortItems($categories3);
        } elseif ($categories2) {
            $data = $this->sortItems($categories2);
        } elseif ($categories) {
            $data = $this->sortItems($categories);
        } else {
            $data = $this->sortItems($localities);
        }

        return $data;
    }

    protected function sortItems($data)
    {
        $result = [];

        foreach ($data as $item) {
            $result[strtoupper(mb_substr($item->getName(), 0, 1))][] = $item;
        }

        ksort($result);

        return $result;
    }

    public function checkCatalogRedirect($slugs, $entities)
    {
        return $this->checkCatalogSlug($slugs['locality'], $entities['locality']) and
            $this->checkCatalogSlug($slugs['category'], $entities['category']) and
            $this->checkCatalogSlug($slugs['category2'], $entities['category2']) and
            $this->checkCatalogSlug($slugs['category3'], $entities['category3']) and
            $this->checkCatalogCategory($entities);
    }

    private function checkCatalogCategory($data)
    {
        $isValid = true;

        if (!empty($data['category'])) {
            if ($data['category']->getParent()) {
                $isValid = false;
            } else {
                if (!empty($data['category2'])) {
                    if ($data['category2']->getParent() != $data['category']) {
                        $isValid = false;
                    } else {
                        if (!empty($data['category3'])) {
                            if ($data['category3']->getParent() != $data['category2']) {
                                $isValid = false;
                            }
                        }
                    }
                }
            }
        }

        return $isValid;
    }

    private function checkCatalogSlug($requestSlug, $entity)
    {
        if ($requestSlug and !($entity and $entity->getSlug() == $requestSlug)) {
            return false;
        }

        return true;
    }
}
