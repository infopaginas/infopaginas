<?php

namespace Domain\SearchBundle\Controller;

use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Entity\Locality;
use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\BusinessBundle\Manager\CategoryManager;
use Domain\BusinessBundle\Manager\LocalityManager;
use Domain\BusinessBundle\Util\SlugUtil;
use Domain\ReportBundle\Manager\KeywordsReportManager;
use Domain\SearchBundle\Util\SearchDataUtil;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Domain\ReportBundle\Manager\BusinessOverviewReportManager;
use Domain\SearchBundle\Model\DataType\SearchResultsDTO;
use Domain\BannerBundle\Model\TypeInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SearchController
 * @package Domain\SearchBundle\Controller
 */
class SearchController extends Controller
{
    /**
     * Main Search page
     * @param Request $request
     *
     * @return Response
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
        $allowRedirect = !filter_var($request->get('redirected', false), FILTER_VALIDATE_BOOLEAN);

        if ($searchDTO) {
            if ($searchDTO->checkSearchInMap()) {
                $disableFilters = true;

                $closestLocality = $this->getBusinessProfileManager()->searchClosestLocalityInElastic($searchDTO);
            } else {
                $category = $this->getCategoryManager()->getCategoryByCustomSlug(
                    SlugUtil::convertSlug($searchDTO->query)
                );

                if ($category and !empty($searchDTO->locationValue->locality->getSlug()) and
                    !$searchDTO->locationValue->ignoreLocality and $allowRedirect
                ) {
                    return $this->handleRedirectToCatalog($searchDTO->locationValue->locality, $category);
                }
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

        $bannerManager  = $this->get('domain_banner.manager.banner');
        $banners        = $bannerManager->getBanners(
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
                'banners'           => $banners,
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


    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getClosestLocalityByCoordAction(Request $request)
    {
        $searchManager = $this->get('domain_search.manager.search');
        $searchDTO = $searchManager->getLocalitySearchDTO($request);
        $closestLocality = $this->getBusinessProfileManager()->searchClosestLocalityInElastic($searchDTO);

        return new JsonResponse(['localityId' => $closestLocality->getId()]);
    }

    /**
     * @return BusinessOverviewReportManager
     */
    protected function getBusinessOverviewReportManager() : BusinessOverviewReportManager
    {
        return $this->get('domain_report.manager.business_overview_report_manager');
    }

    /**
     * Source endpoint for jQuery UI Autocomplete plugin in search widget
     *
     * @param Request $request
     *
     * @return JsonResponse
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

    /**
     * @param Request $request
     *
     * @return Response
     */
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

        $bannerManager  = $this->get('domain_banner.manager.banner');
        $banners        = $bannerManager->getBanners(
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
                'banners'           => $banners,
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

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
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

        $bannerManager  = $this->get('domain_banner.manager.banner');
        $banners        = $bannerManager->getBanners(
            [
                TypeInterface::CODE_SEARCH_PAGE_BOTTOM,
                TypeInterface::CODE_SEARCH_PAGE_TOP,
            ]
        );

        $data = [
            'search'        => $searchDTO,
            'results'       => $searchResultsDTO,
            'banners'       => $banners,
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

    /**
     * @param Request $request
     * @param string $localitySlug
     * @param string $categorySlug
     *
     * @return Response
     */
    public function catalogAction(Request $request, $localitySlug = '', $categorySlug = '')
    {
        $searchManager = $this->get('domain_search.manager.search');

        $category   = null;
        $showResults = false;
        $showCatalog = true;
        $allowRedirect = !filter_var($request->get('redirected', false), FILTER_VALIDATE_BOOLEAN);

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
            return $this->handlePermanentRedirect($locality, $category, $categorySlug, $allowRedirect);
        }

        $searchDTO        = null;
        $searchResultsDTO = null;
        $dcDataDTO        = null;
        $schema           = null;
        $locationMarkers  = null;

        $searchData = $this->getSearchDataByRequest($request);

        $locale = ucwords($request->getLocale());

        $bannerManager  = $this->get('domain_banner.manager.banner');
        $banners        = $bannerManager->getBanners(
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
                $searchDTO->setIsRandomized(true);
                $dcDataDTO = $searchManager->getDoubleClickCatalogData($searchDTO);
                $searchResultsDTO = $searchManager->searchCatalog($searchDTO);

                if (!$searchResultsDTO->resultSet && $searchResultsDTO->page != 1) {
                    return $this->redirectToRoute('domain_search_catalog', [
                        'localitySlug' => $localitySlug,
                        'categorySlug' => $categorySlug,
                        'page'         => 1,
                    ]);
                }

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
            if ($allowRedirect) {
                return $this->handleRedirectToSearch($locality, $categorySlug);
            } else {
                throw $this->createNotFoundException();
            }
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

        $pageRouter = 'domain_search_catalog';
        $searchData['localitySlug'] = $localitySlug;
        $searchData['categorySlug'] = $categorySlug;

        return $this->render(
            ':redesign:catalog.html.twig',
            [
                'search'             => $searchDTO,
                'results'            => $searchResultsDTO,
                'seoData'            => $seoData,
                'banners'            => $banners,
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

    /**
     * @param Request $request
     *
     * @return array
     */
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

    /**
     * @param $locality         Locality
     * @param $category         Category
     * @param $categorySlug     string
     * @param $allowRedirect    bool
     *
     * @return RedirectResponse
     */
    private function handlePermanentRedirect(
        $locality = null,
        $category = null,
        $categorySlug = null,
        $allowRedirect = false
    ) {
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

        if (!empty($data['categorySlug']) or !$categorySlug or !$allowRedirect) {
            return $this->redirectToRoute('domain_search_catalog', $data, 301);
        } else {
            return $this->handleRedirectToSearch($locality, $categorySlug);
        }
    }

    /**
     * @param $locality Locality
     * @param $category Category
     *
     * @return RedirectResponse
     */
    private function handleRedirectToCatalog($locality, $category)
    {
        return $this->redirectToRoute(
            'domain_search_catalog',
            [
                'localitySlug' => $locality->getSlug(),
                'categorySlug' => $category->getSlug(),
                'redirected'   => true,
            ]
        );
    }

    /**
     * @param $locality Locality
     * @param $categorySlug string
     *
     * @return RedirectResponse
     */
    private function handleRedirectToSearch($locality, $categorySlug)
    {
        return $this->redirectToRoute(
            'domain_search_index',
            [
                'q'          => SlugUtil::decodeSlug($categorySlug),
                'geo'        => $locality ? $locality->getName() : '',
                'redirected' => true,
            ]
        );
    }
}
