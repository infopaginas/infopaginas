<?php

namespace Domain\SearchBundle\Model\Manager;

use Domain\BusinessBundle\Entity\Category;
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

    public function search(SearchDTO $searchParams, string $locale, $ignoreFilters = false) : SearchResultsDTO
    {
        $search = $this->businessProfileManager->search($searchParams, $locale);
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

        $category = SearchDataUtil::getCategoryFromRequest($request);

        if ($category) {
            $searchDTO->setCategory1($category);
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

        return $searchDTO;
    }

    public function getSearchCatalogDTO($request, $locality, $category, $category2, $category3)
    {
        $location = $this->geolocationManager->buildLocationValueFromRequest($request, false);

        if (!$location) {
            return null;
        }

        $query     = preg_replace("/[^a-zA-Z0-9\s]+/", "", SearchDataUtil::getQueryFromRequest($request));
        $page      = SearchDataUtil::getPageFromRequest($request);

        $limit     = (int) $this->configService->getSetting(ConfigInterface::DEFAULT_RESULTS_PAGE_SIZE)->getValue();
        $searchDTO = SearchDataUtil::buildRequestDTO($query, $location, $page, $limit);

        if ($category instanceof Category) {
            $searchDTO->setCategory1($category);
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
            $searchDTO->getCategory1()
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
            'categorySlug1'   => null,
            'categorySlug2'   => null,
            'categorySlug3'   => null,
        ];
        $categories = $this->getCategoryParents($category);

        foreach ($categories as $item) {
            $data['categorySlug' . $item->getLvl()] = $item->getSlug();
        }

        return $data;
    }

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

            $data = $this->em->getRepository('DomainBusinessBundle:CatalogItem')
                ->checkCatalogItemHasContent($entities['locality'], $currentCategory);
        }

        return $data;
    }

    /**
     * @param Locality[] $localities
     * @param Category[] $categories1
     * @param Category[] $categories2
     * @param Category[] $categories3
     *
     * @return array();
     */
    public function sortCatalogItems($localities, $categories1 = [], $categories2 = [], $categories3 = [])
    {
        if ($categories3) {
            $data = $this->sortItems($categories3);
        } elseif ($categories2) {
            $data = $this->sortItems($categories2);
        } elseif ($categories1) {
            $data = $this->sortItems($categories1);
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
            $this->checkCatalogSlug($slugs['category1'], $entities['category1']) and
            $this->checkCatalogSlug($slugs['category2'], $entities['category2']) and
            $this->checkCatalogSlug($slugs['category3'], $entities['category3']) and
            $this->checkCatalogCategory($entities);
    }

    private function checkCatalogCategory($data)
    {
        $isValid = true;

        if (!empty($data['category1'])) {
            if ($data['category1']->getParent()) {
                $isValid = false;
            } else {
                if (!empty($data['category2'])) {
                    if ($data['category2']->getParent() != $data['category1']) {
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

    public function getSafeSearchString($query)
    {
        $words = $this->getSaveSearchWords($query);

        $search = implode(' ', $words);

        return $search;
    }

    private function getSaveSearchWords($query)
    {
        $words = explode(' ', $query);

        $data = [];

        foreach ($words as $word) {
            $wordLength = mb_strlen($word);

            if ($wordLength >= ElasticSearchManager::AUTO_SUGGEST_BUSINESS_MIN_WORD_LENGTH_ANALYZED) {
                if ($wordLength > ElasticSearchManager::AUTO_SUGGEST_BUSINESS_MAX_WORD_LENGTH_ANALYZED) {
                    $word = mb_substr($word, 0, ElasticSearchManager::AUTO_SUGGEST_BUSINESS_MAX_WORD_LENGTH_ANALYZED);
                }

                $data[] = $this->escapeElasticSpecialCharacter($word);
            }
        }

        return $data;
    }

    private function escapeElasticSpecialCharacter($query)
    {
        //http://npm.taobao.org/package/elasticsearch-sanitize
        //http://lucene.apache.org/core/2_9_4/queryparsersyntax.html#Escaping
        //characters to escape: + - = && || > < ! ( ) { } [ ] ^ " ~ * ? : \ / AND OR NOT space

        $search = [
            '+',
            '-',
            '=',
            '&&',
            '||',
            '>',
            '<',
            '!',
            '(',
            ')',
            '{',
            '}',
            '[',
            ']',
            '^',
            '"',
            '~',
            '*',
            '?',
            ':',
            '\\',
            '/',
            'AND',
            'OR',
            'NOT',
        ];

        $escaped = [
            '\+',
            '\-',
            '\=',
            '\&\&',
            '\|\|',
            '\>',
            '\<',
            '\!',
            '\(',
            '\)',
            '\{',
            '\}',
            '\[',
            '\]',
            '\^',
            '\"',
            '\~',
            '\*',
            '\?',
            '\:',
            '\\\\',
            '\/',
            '\A\N\D',
            '\O\R',
            '\N\O\T',
        ];

        $newQuery = str_replace($search, $escaped, $query);

        return $newQuery;
    }
}
