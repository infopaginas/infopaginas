<?php

namespace Domain\SearchBundle\Model\Manager;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Category;
use Domain\EmergencyBundle\Entity\EmergencyArea;
use Domain\EmergencyBundle\Entity\EmergencyBusiness;
use Domain\EmergencyBundle\Entity\EmergencyCategory;
use Domain\SearchBundle\Model\DataType\EmergencySearchDTO;
use Domain\SiteBundle\Utils\Helpers\LocaleHelper;
use Oxa\ElasticSearchBundle\Manager\ElasticSearchManager;
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
use Domain\BusinessBundle\Entity\CatalogItem;

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
        $this->businessProfileManager   = $businessProfileManager;
        $this->categoriesManager        = $categoryManager;
        $this->geolocationManager       = $geolocationManager;
        $this->localityManager          = $localityManager;
    }

    /**
     * @param SearchDTO $searchParams
     * @param bool      $ignoreFilters
     *
     * @return SearchResultsDTO
     */
    public function search(SearchDTO $searchParams, $ignoreFilters = false) : SearchResultsDTO
    {
        $search = $this->businessProfileManager->search($searchParams);
        $results = $search['data'];
        $totalResults = $search['total'];

        if (!$results) {
            // todo - change logic to 40 miles
            $results  = [];
        }

        $categories    = [];
        $neighborhoods = [];

        if ($results) {
            if (!$ignoreFilters) {
                $categories = $this->categoriesManager->getCategoriesByProfiles($results);
            }

            $pagesCount = ceil($totalResults/$searchParams->limit);
        } else {
            $totalResults = 0;
            $pagesCount   = 0;
        }

        if (!$ignoreFilters) {
            $neighborhoods = $this->localityManager->getLocalityNeighborhoods($searchParams->locationValue->locality);
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

    /**
     * @param SearchDTO $searchParams
     *
     * @return SearchResultsDTO
     */
    public function searchCatalog(SearchDTO $searchParams) : SearchResultsDTO
    {
        $search = $this->businessProfileManager->searchCatalog($searchParams);

        $results = $search['data'];
        $totalResults = $search['total'];

        if ($results) {
            $pagesCount   = ceil($totalResults/$searchParams->limit);
            $categories = $this->categoriesManager->getCategoriesByProfiles($results, $searchParams->getLocale());
            $neighborhoods = $this->localityManager->getLocalityNeighborhoods($searchParams->locationValue->locality);
        } else {
            $totalResults  = 0;
            $pagesCount    = 0;
            $categories    = [];
            $neighborhoods = [];
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

    /**
     * @param SearchDTO $searchParams
     *
     * @return SearchResultsDTO
     */
    public function searchSuggestedBusinesses(SearchDTO $searchParams) : SearchResultsDTO
    {
        $search = $this->businessProfileManager->searchSuggestedBusinesses($searchParams);

        $results      = $search['data'];
        $totalResults = $search['total'];

        if ($results) {
            $pagesCount = ceil($totalResults/$searchParams->limit);
        } else {
            $totalResults = 0;
            $pagesCount   = 0;
        }

        $response = SearchDataUtil::buildResponceDTO(
            $results,
            $totalResults,
            $searchParams->page,
            $pagesCount
        );

        return $response;
    }

    /**
     * @param SearchDTO $searchParams
     *
     * @return array
     */
    public function searchClosestBusinessesApi(SearchDTO $searchParams)
    {
        $search = $this->businessProfileManager->searchClosestBusinesses($searchParams);

        $response = [
            'total' => $search['total'],
            'data'  => [],
        ];

        foreach ($search['data'] as $businessProfile) {
            /** @var BusinessProfile $businessProfile */
            $response['data'][] = [
                'id'  => $businessProfile->getId(),
                'uid' => $businessProfile->getUid(),
                'name' => [
                    'en' => $businessProfile->getName(),
                    'es' => $businessProfile->getName(),
                ],
                'location' => [
                    'lat' => $businessProfile->getLatitude(),
                    'lng' => $businessProfile->getLongitude(),
                ],
            ];
        }

        return $response;
    }

    /**
     * @param EmergencySearchDTO $searchParams
     *
     * @return EmergencyBusiness[]
     */
    public function searchEmergencyBusinessByAreaAndCategory($searchParams)
    {
        $search = $this->businessProfileManager->searchEmergencyBusinesses($searchParams);

        return $search['data'];
    }

    /**
     * @param Request $request
     * @param int     $areaId
     * @param int     $categoryId
     *
     * @return EmergencySearchDTO
     */
    public function getEmergencySearchDTO(Request $request, $areaId, $categoryId)
    {
        $page    = SearchDataUtil::getPageFromRequest($request);
        $limit   = (int) $this->configService->getSetting(ConfigInterface::DEFAULT_RESULTS_PAGE_SIZE)->getValue();

        $latitude  = SearchDataUtil::getEmergencyCatalogLatitudeFromRequest($request);
        $longitude = SearchDataUtil::getEmergencyCatalogLongitudeFromRequest($request);

        $characterFilter = SearchDataUtil::getEmergencyCatalogCharFilterFromRequest($request);

        if (!$latitude and !$longitude) {
            $orderBy = SearchDataUtil::EMERGENCY_ORDER_BY_ALPHABET;
        } else {
            $orderBy = SearchDataUtil::getEmergencyCatalogOrderByFromRequest($request);
        }

        $searchDTO  = SearchDataUtil::buildEmergencyRequestDTO($page, $limit, $areaId, $categoryId, $orderBy);

        if ($orderBy == SearchDataUtil::EMERGENCY_ORDER_BY_DISTANCE) {
            $searchDTO->lat = $latitude;
            $searchDTO->lng = $longitude;
        }

        if ($characterFilter) {
            $searchDTO->characterFilter = $characterFilter;
        }

        $serviceFilters = SearchDataUtil::getEmergencyServiceFiltersFromRequest($request);

        if ($serviceFilters) {
            $searchDTO->serviceIds = $serviceFilters;
        }

        return $searchDTO;
    }

    /**
     * @param Request $request
     *
     * @return SearchDTO
     */
    public function getLocalitySearchDTO(Request $request)
    {
        $location = $this->geolocationManager->buildLocationValueFromRequest($request);
        $searchDTO  = SearchDataUtil::buildRequestDTO('', $location, 1, 1);

        return $searchDTO;
    }

    /**
     * @param Request $request
     * @param bool $isRandomized
     *
     * @return SearchDTO
     */
    public function getSearchDTO(Request $request, $isRandomized = true)
    {
        $location = $this->geolocationManager->buildLocationValueFromRequest($request);
        $query = $this->getSafeSearchString(SearchDataUtil::getQueryFromRequest($request));

        if (!$location or !$query) {
            return null;
        }

        $page       = SearchDataUtil::getPageFromRequest($request);

        $limit      = (int) $this->configService->getSetting(ConfigInterface::DEFAULT_RESULTS_PAGE_SIZE)->getValue();
        $searchDTO  = SearchDataUtil::buildRequestDTO($query, $location, $page, $limit);
        $searchDTO  = $this->setSearchAdsParams($searchDTO);

        $categoryFilter = SearchDataUtil::getCategoryFromRequest($request);

        if ($categoryFilter) {
            $searchDTO->setCategoryFilter($categoryFilter);
        }

        $neighborhood = SearchDataUtil::getNeighborhoodFromRequest($request);

        if ($neighborhood) {
            $searchDTO->setNeighborhood($neighborhood);
        }

        $orderBy = SearchDataUtil::getOrderByFromRequest($request);

        if ($orderBy) {
            $searchDTO->setOrderBy($orderBy);
        }

        if (!$searchDTO->checkSearchInMap()) {
            $searchDTO->setIsRandomized($isRandomized);
        }


        if ($request->getLocale()) {
            $locale = LocaleHelper::getLocale($request->getLocale());
            $searchDTO->setLocale($locale);
        }

        return $searchDTO;
    }

    /**
     * @param array $params
     *
     * @return SearchDTO
     */
    public function getSearchApiDTO($params)
    {
        $location = $this->geolocationManager->buildLocationValueFromApi($params);

        $query = $this->getSafeSearchString($params['q']);

        $searchDTO  = SearchDataUtil::buildRequestDTO($query, $location, (int)$params['p'], (int)$params['pp']);
        $searchDTO  = $this->setSearchAdsParams($searchDTO);

        $searchDTO->setOrderBy(SearchDataUtil::ORDER_BY_DISTANCE);

        return $searchDTO;
    }

    /**
     * @param Request $request
     * @param Locality $locality
     * @param category $category
     *
     * @return SearchDTO
     */
    public function getSearchCatalogDTO($request, $locality, $category)
    {
        $location = $this->geolocationManager->buildCatalogLocationValue($locality);

        if (!$location) {
            return null;
        }

        $query     = preg_replace("/[^a-zA-Z0-9\s]+/", "", SearchDataUtil::getQueryFromRequest($request));
        $page      = SearchDataUtil::getPageFromRequest($request);

        $limit     = (int) $this->configService->getSetting(ConfigInterface::DEFAULT_RESULTS_PAGE_SIZE)->getValue();
        $searchDTO = SearchDataUtil::buildRequestDTO($query, $location, $page, $limit);
        $searchDTO = $this->setSearchAdsParams($searchDTO);

        if ($category instanceof Category) {
            $searchDTO->setCategory($category);
        }

        if ($locality) {
            $searchDTO->setCatalogLocality($locality);
        }

        $categoryFilter = SearchDataUtil::getCategoryFromRequest($request);

        if ($categoryFilter) {
            $searchDTO->setCategoryFilter($categoryFilter);
        }

        $neighborhood = SearchDataUtil::getNeighborhoodFromRequest($request);

        if ($neighborhood) {
            $searchDTO->setNeighborhood($neighborhood);
        }

        $orderBy = SearchDataUtil::getOrderByFromRequest($request);

        if ($orderBy) {
            $searchDTO->setOrderBy($orderBy);
        }

        if ($request->getLocale()) {
            $locale = LocaleHelper::getLocale($request->getLocale());
            $searchDTO->setLocale($locale);
        }

        return $searchDTO;
    }

    /**
     * @param Request         $request
     * @param BusinessProfile $business
     *
     * @return SearchDTO
     */
    public function getSearchSuggestedBusinessesDTO($request, $business)
    {
        $localities = $business->getLocalities();
        $categories = $business->getCategories();

        $locality = $business->getCatalogLocality();
        $location = $this->geolocationManager->buildCatalogLocationValue($locality);

        $query = '';
        $page  = SearchDataUtil::getPageFromRequest($request);

        $limit     = (int) $this->configService->getSetting(ConfigInterface::SEARCH_ADS_PER_PAGE)->getValue();
        $searchDTO = SearchDataUtil::buildRequestDTO($query, $location, $page, $limit);
        $searchDTO = $this->setSearchAdsParams($searchDTO);

        if ($categories) {
            $searchDTO->setSuggestedCategories(BusinessProfileUtil::extractEntitiesId($categories->toArray()));
        }

        if ($localities) {
            $searchDTO->setSuggestedLocalities(BusinessProfileUtil::extractEntitiesId($localities->toArray()));
        }

        $searchDTO->setMinimumCategoriesMatch(
            (int) $this->configService->getSetting(ConfigInterface::SUGGEST_CATEGORY_MINIMUM_MATCH)->getValue()
        );
        $searchDTO->setMinimumLocalitiesMatch(
            (int) $this->configService->getSetting(ConfigInterface::SUGGEST_LOCALITY_MINIMUM_MATCH)->getValue()
        );

        $orderBy = SearchDataUtil::getOrderByFromRequest($request);

        if ($orderBy) {
            $searchDTO->setOrderBy($orderBy);
        }

        if ($request->getLocale()) {
            $locale = LocaleHelper::getLocale($request->getLocale());
            $searchDTO->setLocale($locale);
        }

        return $searchDTO;
    }

    /**
     * @param SearchDTO $searchDTO
     *
     * @return DCDataDTO
     */
    public function getDoubleClickData(SearchDTO $searchDTO) : DCDataDTO
    {
        $categoriesSlugSet = [];

        $categorySlug = $this->getCategorySlugFromFilters($searchDTO);

        if ($categorySlug) {
            $categoriesSlugSet[] = $categorySlug;
        }

        return new DCDataDTO(
            explode(' ', $searchDTO->query),
            $searchDTO->locationValue->name,
            $categoriesSlugSet
        );
    }

    /**
     * @param SearchDTO $searchDTO
     *
     * @return DCDataDTO
     */
    public function getDoubleClickCatalogData(SearchDTO $searchDTO) : DCDataDTO
    {
        $categoriesSlugSet = [];

        if ($searchDTO->getCategory()) {
            $categoriesSlugSet[] = $searchDTO->getCategory()->getSlug();
        }

        return new DCDataDTO(
            explode(' ', $searchDTO->query),
            $searchDTO->locationValue->name,
            $categoriesSlugSet
        );
    }

    /**
     * @param SearchDTO $searchDTO
     *
     * @return string
     */
    protected function getCategorySlugFromFilters(SearchDTO $searchDTO)
    {
        $categorySlug = '';

        if ($searchDTO->getCategory()) {
            $category = $this->categoriesManager->getRepository()->find((int)$searchDTO->getCategory());

            if ($category) {
                $categorySlug = $category->getSlug();
            }
        }

        return $categorySlug;
    }

    /**
     * @param string $localitySlug
     *
     * @return Locality|null
     */
    public function searchCatalogLocality($localitySlug)
    {
        $locality = null;

        if ($localitySlug) {
            $locality = $this->localityManager->getLocalityBySlug($localitySlug);

            if (!$locality) {
                $locality = $this->localityManager->getLocalityByLocalityPseudoSlug($localitySlug);
            }
        }

        return $locality;
    }

    /**
     * @param string $categorySlug
     *
     * @return Category|null
     */
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

    /**
     * @param array $entities
     *
     * @return bool
     */
    public function checkCatalogItemHasContent($entities)
    {
        $data = true;

        if ($entities['locality']) {
            if (!empty($entities['category3'])) {
                $currentCategory = $entities['category3'];
            } elseif (!empty($entities['category2'])) {
                $currentCategory = $entities['category2'];
            } elseif (!empty($entities['category1'])) {
                $currentCategory = $entities['category1'];
            } else {
                $currentCategory = null;
            }

            $data = $this->em->getRepository(CatalogItem::class)
                ->checkCatalogItemHasContent($entities['locality'], $currentCategory);
        }

        return $data;
    }

    /**
     * @param Locality[] $localities
     * @param Category[] $categories
     *
     * @return array();
     */
    public function sortCatalogItems($localities, $categories)
    {
        if ($categories) {
            $data = $this->sortItems($categories);
        } else {
            $data = $this->sortItems($localities);
        }

        return $data;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function sortItems($data)
    {
        $result = [];

        foreach ($data as $item) {
            $result[strtoupper(mb_substr($item->getName(), 0, 1))][] = $item;
        }

        ksort($result);

        return $result;
    }

    /**
     * @param array $slugs
     * @param array $entities
     *
     * @return bool
     */
    public function checkCatalogRedirect($slugs, $entities)
    {
        return $this->checkCatalogSlug($slugs['locality'], $entities['locality']) and
            $this->checkCatalogSlug($slugs['category'], $entities['category']);
    }

    /**
     * @param string $requestSlug
     * @param mixed $entity
     *
     * @return bool
     */
    private function checkCatalogSlug($requestSlug, $entity)
    {
        if ($requestSlug and !($entity and $entity->getSlug() == $requestSlug)) {
            return false;
        }

        return true;
    }

    /**
     * @param string $query
     *
     * @return string
     */
    public function getSafeSearchString($query)
    {
        $words = $this->getSaveSearchWords($query);

        $search = implode(' ', $words);

        return $search;
    }

    /**
     * @param string $query
     *
     * @return array
     */
    private function getSaveSearchWords($query)
    {
        $searchString = SearchDataUtil::sanitizeElasticSearchQueryString($query);

        $words = explode(' ', $searchString);

        $data = [];

        foreach ($words as $word) {
            $wordLength = mb_strlen($word);

            if ($wordLength >= ElasticSearchManager::AUTO_SUGGEST_BUSINESS_MIN_WORD_LENGTH_ANALYZED) {
                if ($wordLength > ElasticSearchManager::AUTO_SUGGEST_BUSINESS_MAX_WORD_LENGTH_ANALYZED) {
                    $word = mb_substr($word, 0, ElasticSearchManager::AUTO_SUGGEST_BUSINESS_MAX_WORD_LENGTH_ANALYZED);
                }

                $data[] = $word;
            }
        }

        return $data;
    }

    /**
     * @param $searchDTO SearchDTO
     *
     * @return SearchDTO
     */
    protected function setSearchAdsParams($searchDTO)
    {
        $adsAllowed = (bool) $this->configService->getSetting(ConfigInterface::SEARCH_ADS_ALLOWED)->getValue();

        if ($adsAllowed) {
            $adsPerPage  = (int) $this->configService->getSetting(ConfigInterface::SEARCH_ADS_PER_PAGE)->getValue();

            if ($adsPerPage and $adsPerPage > 0) {
                $adsMaxPages = (int) $this->configService->getSetting(ConfigInterface::SEARCH_ADS_MAX_PAGE)->getValue();

                $searchDTO->adsMaxPages = $adsMaxPages;
                $searchDTO->adsPerPage  = $adsPerPage;
                $searchDTO->adsAllowed  = $adsAllowed;
            }
        }

        return $searchDTO;
    }
}
