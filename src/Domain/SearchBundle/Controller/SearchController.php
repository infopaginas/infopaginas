<?php

namespace Domain\SearchBundle\Controller;

use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\BusinessBundle\Manager\CategoryManager;
use Domain\BusinessBundle\Manager\LocalityManager;
use Domain\ReportBundle\Manager\KeywordsReportManager;
use Domain\SearchBundle\Util\SearchDataUtil;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Domain\ReportBundle\Manager\BusinessOverviewReportManager;
use Domain\SearchBundle\Model\DataType\SearchResultsDTO;
use Domain\BannerBundle\Model\TypeInterface;

/**
 * Class SearchController
 * @package Domain\SearchBundle\Controller
 */
class SearchController extends Controller
{
    /**
     * Main Search page
     */
    public function indexAction(Request $request)
    {
        $searchManager = $this->get('domain_search.manager.search');

        $searchDTO = $searchManager->getSearchDTO($request);

        $searchData = $this->getSearchDataByRequest($request);

        $locale = ucwords($request->getLocale());

        $schema       = false;
        $locationName = false;
        $closestLocality = '';
        $disableFilters = false;
        $seoCategories = [];

        if ($searchDTO) {
            if ($searchDTO->checkSearchInMap()) {
                $disableFilters = true;

                $closestLocality = $this->getBusinessProfileManager()->searchClosestLocalityInElastic($searchDTO);
            }

            $searchResultsDTO = $searchManager->search($searchDTO, $locale, $disableFilters);
            $dcDataDTO        = $searchManager->getDoubleClickData($searchDTO);

            $this->getBusinessProfileManager()
                ->trackBusinessProfilesCollectionImpressions($searchResultsDTO->resultSet);
            $this->getKeywordsReportManager()
                ->saveProfilesDataSuggestedBySearchQuery($searchData['q'], $searchResultsDTO->resultSet);

            $schema = $this->getBusinessProfileManager()->buildBusinessProfilesSchema($searchResultsDTO->resultSet);

            $locationMarkers = $this->getBusinessProfileManager()
                ->getLocationMarkersFromProfileData($searchResultsDTO->resultSet);

            if ($closestLocality) {
                $locationName = $closestLocality->getName();
                $searchData['geo'] = $locationName;
            } elseif ($searchDTO->locationValue) {
                $locationName = $searchDTO->locationValue->name;
            }

            if ($searchDTO->query) {
                $seoCategories[] = $searchDTO->query;
            }

            $this->getBusinessOverviewReportManager()->registerBusinessImpression($searchResultsDTO->resultSet);
        } else {
            $searchResultsDTO = null;
            $dcDataDTO        = null;
            $locationMarkers  = $this->getBusinessProfileManager()->getDefaultLocationMarkers();
        }

        $bannerFactory = $this->get('domain_banner.factory.banner');
        $bannerFactory->prepareBanners(
            [
                TypeInterface::CODE_SEARCH_PAGE_BOTTOM,
                TypeInterface::CODE_SEARCH_PAGE_TOP,
                TypeInterface::CODE_SEARCH_FLOAT_BOTTOM,
            ]
        );

        $seoData = $this->getBusinessProfileManager()->getBusinessProfileSearchSeoData($locationName, $seoCategories);

        // hardcode for catalog
        $pageRouter = 'domain_search_index';

        return $this->render(
            ':redesign:search-results.html.twig',
            [
                'search'            => $searchDTO,
                'results'           => $searchResultsDTO,
                'seoData'           => $seoData,
                'bannerFactory'     => $bannerFactory,
                'dcDataDTO'         => $dcDataDTO,
                'searchData'        => $searchData,
                'pageRouter'        => $pageRouter,
                'schemaJsonLD'      => $schema,
                'markers'           => $locationMarkers,
                'noFollowRelevance' => SearchDataUtil::ORDER_BY_RELEVANCE != SearchDataUtil::DEFAULT_ORDER_BY_VALUE,
                'noFollowDistance'  => SearchDataUtil::ORDER_BY_DISTANCE  != SearchDataUtil::DEFAULT_ORDER_BY_VALUE,
                'searchRelevance'   => SearchDataUtil::ORDER_BY_RELEVANCE,
                'searchDistance'    => SearchDataUtil::ORDER_BY_DISTANCE,
                'disableFilters'    => $disableFilters,
            ]
        );
    }

    protected function getBusinessOverviewReportManager() : BusinessOverviewReportManager
    {
        return $this->get('domain_report.manager.business_overview_report_manager');
    }

    /**
     * Source endpoint for jQuery UI Autocomplete plugin in search widget
     */
    public function autocompleteAction(Request $request)
    {
        $searchData = $this->getSearchDataByRequest($request);

        $businessProfileManager = $this->get('domain_business.manager.business_profile');
        $results = $businessProfileManager->searchCategoryAutosuggestByPhrase(
            $searchData['q'],
            $request->getLocale(),
            CategoryManager::AUTO_SUGGEST_MAX_CATEGORY_MAIN_COUNT
        );

        return (new JsonResponse)->setData($results);
    }

    /**
     * autocomplete locality by name
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function autocompleteLocalityAction(Request $request)
    {
        $locale = $request->getLocale();
        $term   = $request->get('term', '');

        $localityManager = $this->get('domain_business.manager.locality');
        $data = $localityManager->getLocalitiesAutocomplete($term, $locale);

        return (new JsonResponse)->setData($data);
    }

    public function compareAction(Request $request)
    {
        $searchManager = $this->get('domain_search.manager.search');

        $searchDTO = $searchManager->getSearchDTO($request);

        $searchData = $this->getSearchDataByRequest($request);

        $locale = ucwords($request->getLocale());

        $locationName = false;
        $closestLocality = '';
        $seoCategories = [];

        if ($searchDTO) {
            if ($searchDTO->checkSearchInMap()) {
                $closestLocality = $this->getBusinessProfileManager()->searchClosestLocalityInElastic($searchDTO);
            }

            $searchResultsDTO   = $searchManager->search($searchDTO, $locale);

            $this->getBusinessProfileManager()
                ->trackBusinessProfilesCollectionImpressions($searchResultsDTO->resultSet);

            $this->getKeywordsReportManager()
                ->saveProfilesDataSuggestedBySearchQuery($searchData['q'], $searchResultsDTO->resultSet);

            $schema = $this->getBusinessProfileManager()->buildBusinessProfilesSchema($searchResultsDTO->resultSet);

            if ($closestLocality) {
                $locationName = $closestLocality->getName();
                $searchData['geo'] = $locationName;
            } elseif ($searchDTO->locationValue) {
                $locationName = $searchDTO->locationValue->name;
            }

            if ($searchDTO->query) {
                $seoCategories[] = $searchDTO->query;
            }

            $this->getBusinessOverviewReportManager()->registerBusinessImpression($searchResultsDTO->resultSet);
        } else {
            $searchResultsDTO = null;
            $schema           = null;
        }

        $bannerFactory  = $this->get('domain_banner.factory.banner');
        $bannerFactory->prepareBanners(
            [
                TypeInterface::CODE_COMPARE_PAGE_BOTTOM,
                TypeInterface::CODE_COMPARE_PAGE_TOP,
            ]
        );

        $pageRouter = $this->container->get('request')->attributes->get('_route');

        $seoData = $this->getBusinessProfileManager()->getBusinessProfileSearchSeoData($locationName, $seoCategories);

        return $this->render(
            ':redesign:search-results-compare.html.twig',
            [
                'search'            => $searchDTO,
                'results'           => $searchResultsDTO,
                'seoData'           => $seoData,
                'bannerFactory'     => $bannerFactory,
                'searchData'        => $searchData,
                'pageRouter'        => $pageRouter,
                'schemaJsonLD'      => $schema,
                'noFollowRelevance' => SearchDataUtil::ORDER_BY_RELEVANCE != SearchDataUtil::DEFAULT_ORDER_BY_VALUE,
                'noFollowDistance'  => SearchDataUtil::ORDER_BY_DISTANCE  != SearchDataUtil::DEFAULT_ORDER_BY_VALUE,
                'searchRelevance'   => SearchDataUtil::ORDER_BY_RELEVANCE,
                'searchDistance'    => SearchDataUtil::ORDER_BY_DISTANCE,
            ]
        );
    }

    public function mapAction(Request $request)
    {
        $searchManager = $this->get('domain_search.manager.search');

        $searchDTO = $searchManager->getSearchDTO($request);

        $searchData = $this->getSearchDataByRequest($request);

        $locale = ucwords($request->getLocale());

        $locationName  = '';
        $seoCategories = [];

        if ($searchDTO) {
            $closestLocality = $this->getBusinessProfileManager()->searchClosestLocalityInElastic($searchDTO);

            if ($closestLocality) {
                $searchDTO->locationValue->name = $closestLocality->getName();
                $searchDTO->locationValue->lat  = $closestLocality->getLatitude();
                $searchDTO->locationValue->lng  = $closestLocality->getLongitude();
                $searchDTO->locationValue->locality = $closestLocality;
            }

            $searchResultsDTO = $searchManager->search($searchDTO, $locale, true);
            $dcDataDTO        = $searchManager->getDoubleClickData($searchDTO);

            $this->getBusinessProfileManager()
                ->trackBusinessProfilesCollectionImpressions($searchResultsDTO->resultSet);
            $this->getKeywordsReportManager()
                ->saveProfilesDataSuggestedBySearchQuery($searchData['q'], $searchResultsDTO->resultSet);

            $locationMarkers = $this->getBusinessProfileManager()
                ->getLocationMarkersFromProfileData($searchResultsDTO->resultSet);

            if ($closestLocality) {
                $locationName = $closestLocality->getName();
                $request->query->set('geo', $locationName);
            }

            if ($searchDTO->query) {
                $seoCategories[] = $searchDTO->query;
            }

            $this->getBusinessOverviewReportManager()->registerBusinessImpression($searchResultsDTO->resultSet);
        } else {
            $searchResultsDTO = null;
            $dcDataDTO        = null;
            $locationMarkers  = $this->getBusinessProfileManager()->getDefaultLocationMarkers();
        }

        $seoData = $this->getBusinessProfileManager()->getBusinessProfileSearchSeoData($locationName, $seoCategories);

        $bannerFactory = $this->get('domain_banner.factory.banner');
        $bannerFactory->prepareBanners(
            [
                TypeInterface::CODE_SEARCH_PAGE_BOTTOM,
                TypeInterface::CODE_SEARCH_PAGE_TOP,
            ]
        );

        $data = [
            'search'        => $searchDTO,
            'results'       => $searchResultsDTO,
            'bannerFactory' => $bannerFactory,
        ];

        $html = $this->renderView(
            ':redesign/blocks:search_result_item_ajax.html.twig',
            $data
        );

        $router = $this->get('router');

        $staticSearchUrl  = $router->generate('domain_search_index', $request->query->all(), true);
        $staticCompareUrl = $router->generate('domain_search_compare', $request->query->all(), true);

        return new JsonResponse(
            [
                'html'      => $html,
                'seoData'   => $seoData,
                'markers'   => $locationMarkers,
                'targeting' => $dcDataDTO,
                'location'  => $locationName,
                'staticSearchUrl'  => $staticSearchUrl,
                'staticCompareUrl' => $staticCompareUrl,
            ]
        );
    }

    /**
     * @return KeywordsReportManager
     */
    protected function getKeywordsReportManager() : KeywordsReportManager
    {
        return $this->get('domain_report.manager.keywords_report_manager');
    }

    /**
     * @return \Domain\BusinessBundle\Manager\BusinessProfileManager
     */
    protected function getBusinessProfileManager() : BusinessProfileManager
    {
        return $this->get('domain_business.manager.business_profile');
    }

    /**
     * @return \Domain\BusinessBundle\Manager\LocalityManager
     */
    protected function getLocalityManager() : LocalityManager
    {
        return $this->get('domain_business.manager.locality');
    }

    /**
     * @return \Domain\BusinessBundle\Manager\CategoryManager
     */
    protected function getCategoryManager() : CategoryManager
    {
        return $this->get('domain_business.manager.category');
    }

    public function catalogAction(Request $request, $localitySlug = '', $categorySlug = '') {
        $searchManager = $this->get('domain_search.manager.search');

        $category   = null;
        $showResults = false;
        $showCatalog = true;

        $seoLocationName  = null;
        $seoCategories = [];

        $categories = [];

        $localities = $this->getLocalityManager()->getCatalogLocalitiesWithContent();

        $locality = $searchManager->searchCatalogLocality($localitySlug);

        $request->attributes->set('geo', $localitySlug);
        $request->attributes->set('q', $localitySlug);

        if ($locality) {
            $categories = $this->getCategoryManager()
                ->getAvailableCategoriesWithContent($locality, $request->getLocale());

            $request->attributes->set('catalogLocality', $locality->getName());
            $request->attributes->set('geo', $locality->getName());
            $request->attributes->set('q', $locality->getName());

            $category = $searchManager->searchCatalogCategory($categorySlug);

            $seoLocationName = $locality->getName();

            if ($category) {
                $request->attributes->set('category', $category->getName());
                $request->attributes->set('q', $category->getName());
                $showCatalog = false;

                $showResults = true;
                $seoCategories[] = $category->getName();
            }
        }

        $slugs = [
            'locality'  => $localitySlug,
            'category' => $categorySlug,
        ];

        $entities = [
            'locality'  => $locality,
            'category' => $category,
        ];

        if (!$searchManager->checkCatalogRedirect($slugs, $entities)) {
            return $this->handlePermanentRedirect($locality, $category);
        }

        $searchDTO        = null;
        $searchResultsDTO = null;
        $dcDataDTO        = null;
        $schema           = null;
        $locationMarkers  = null;

        $searchData = $this->getSearchDataByRequest($request);

        $locale = ucwords($request->getLocale());

        $bannerFactory = $this->get('domain_banner.factory.banner');
        $bannerFactory->prepareBanners(
            [
                TypeInterface::CODE_SEARCH_PAGE_BOTTOM,
                TypeInterface::CODE_SEARCH_PAGE_TOP,
                TypeInterface::CODE_SEARCH_FLOAT_BOTTOM,
            ]
        );

        if (!$locality) {
            $locationMarkers = $this->getBusinessProfileManager()->getLocationMarkersFromLocalityData($localities);
        } elseif (!$category) {
            $locationMarkers = $this->getBusinessProfileManager()->getLocationMarkersFromLocalityData([$locality]);
        } else {
            $searchDTO = $searchManager->getSearchCatalogDTO($request, $locality, $category);

            //locality lat and lan required
            if ($searchDTO) {
                $dcDataDTO = $searchManager->getDoubleClickCatalogData($searchDTO);
                $searchResultsDTO = $searchManager->searchCatalog($searchDTO);

                $this->getBusinessProfileManager()
                    ->trackBusinessProfilesCollectionImpressions($searchResultsDTO->resultSet);

                $this->getKeywordsReportManager()
                    ->saveProfilesDataSuggestedBySearchQuery($searchData['q'], $searchResultsDTO->resultSet);

                $schema = $this->getBusinessProfileManager()->buildBusinessProfilesSchema($searchResultsDTO->resultSet);

                $locationMarkers = $this->getBusinessProfileManager()
                    ->getLocationMarkersFromProfileData($searchResultsDTO->resultSet);

                $this->getBusinessOverviewReportManager()->registerBusinessImpression($searchResultsDTO->resultSet);
            }
        }

        if (!$searchManager->checkCatalogItemHasContent($entities)) {
            throw $this->createNotFoundException();
        }

        if (!$locationMarkers) {
            $locationMarkers = $this->getBusinessProfileManager()->getDefaultLocationMarkers();
        }

        $catalogLevelItems = $searchManager->sortCatalogItems($localities, $categories);

        $seoData = $this->getBusinessProfileManager()->getBusinessProfileSearchSeoData(
            $seoLocationName,
            $seoCategories,
            true
        );

        // hardcode for catalog
        $pageRouter = 'domain_search_index';

        return $this->render(
            ':redesign:catalog.html.twig',
            [
                'search'             => $searchDTO,
                'results'            => $searchResultsDTO,
                'seoData'            => $seoData,
                'bannerFactory'      => $bannerFactory,
                'dcDataDTO'          => $dcDataDTO,
                'searchData'         => $searchData,
                'pageRouter'         => $pageRouter,
                'currentLocality'    => $locality,
                'currentCategory'    => $category,
                'schemaJsonLD'       => $schema,
                'markers'            => $locationMarkers,
                'catalogLevelItems'  => $catalogLevelItems,
                'showResults'        => $showResults,
                'showCatalog'        => $showCatalog,
                'noFollowRelevance'  => SearchDataUtil::ORDER_BY_RELEVANCE != SearchDataUtil::DEFAULT_ORDER_BY_VALUE,
                'noFollowDistance'   => SearchDataUtil::ORDER_BY_DISTANCE  != SearchDataUtil::DEFAULT_ORDER_BY_VALUE,
                'searchRelevance'    => SearchDataUtil::ORDER_BY_RELEVANCE,
                'searchDistance'     => SearchDataUtil::ORDER_BY_DISTANCE,
            ]
        );
    }

    private function getSearchDataByRequest(Request $request)
    {
        $keys = [
            'q',
            'geo',
            'order',
            'category',
            'neighborhood',

            // geo location
            'lat',
            'lng',
            'geoLoc',
        ];

        $searchData = [];

        foreach ($keys as $key) {
            $searchData[$key] = $request->get($key, '');
        }

        return $searchData;
    }

    private function handlePermanentRedirect($locality = null, $category = null)
    {
        $data = [
            'localitySlug'    => null,
            'categorySlug'    => null,
        ];

        if ($locality) {
            if ($category) {
                $data['categorySlug'] = $category->getSlug();
            }

            $data['localitySlug'] = $locality->getSlug();
        }

        return $this->redirectToRoute(
            'domain_search_catalog',
            $data,
            301
        );
    }
}
