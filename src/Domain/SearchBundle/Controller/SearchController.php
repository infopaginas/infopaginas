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
        $results = $businessProfileManager->searchAutosuggestByPhraseAndLocation(
            $searchData['q'],
            $request->getLocale()
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
            $searchResultsDTO = $searchManager->search($searchDTO, $locale, true);
            $dcDataDTO        = $searchManager->getDoubleClickData($searchDTO);

            $closestLocality = $this->getBusinessProfileManager()->searchClosestLocalityInElastic($searchDTO);

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

    public function catalogAction(
        Request $request,
        $localitySlug = '',
        $categorySlug1 = '',
        $categorySlug2 = '',
        $categorySlug3 = ''
    ) {
        $searchManager = $this->get('domain_search.manager.search');

        $category1   = null;
        $category2   = null;
        $category3   = null;
        $showResults = null;
        $showCatalog = true;

        $seoLocationName  = null;
        $seoCategories = [];

        $categories1 = [];
        $categories2 = [];
        $categories3 = [];

        $localities = $this->getLocalityManager()->getCatalogLocalitiesWithContent();

        $locality = $searchManager->searchCatalogLocality($localitySlug);

        $request->attributes->set('geo', $localitySlug);
        $request->attributes->set('q', $localitySlug);

        if ($locality) {
            $categories1 = $this->getCategoryManager()
                ->getAvailableParentCategoriesWithContent($locality, $request->getLocale());

            $request->attributes->set('catalogLocality', $locality->getName());
            $request->attributes->set('geo', $locality->getName());
            $request->attributes->set('q', $locality->getName());

            $category1 = $searchManager->searchCatalogCategory($categorySlug1);

            $seoLocationName = $locality->getName();

            if ($category1 and !$category1->getParent()) {
                $request->attributes->set('category', $category1->getName());
                $request->attributes->set('q', $category1->getName());

                $categories2 = $this->getCategoryManager()->searchSubcategoriesWithContentByCategory(
                    $category1,
                    $locality,
                    Category::CATEGORY_LEVEL_2,
                    $request->getLocale()
                );

                $category2   = $searchManager->searchCatalogCategory($categorySlug2);

                if (!$categories2) {
                    $showCatalog = false;
                }

                $showResults = true;
                $seoCategories[] = $category1->getName();

                if ($category2 and $category2->getParent()) {
                    $request->attributes->set('subcategory', $category2->getName());
                    $request->attributes->set('q', $category2->getName());

                    $categories3 = $this->getCategoryManager()->searchSubcategoriesWithContentByCategory(
                        $category2,
                        $locality,
                        Category::CATEGORY_LEVEL_3,
                        $request->getLocale()
                    );

                    $category3   = $searchManager->searchCatalogCategory($categorySlug3);

                    $seoCategories[] = $category2->getName();

                    if ($category3 and $category3->getParent()) {
                        $request->attributes->set('subcategory', $category3->getName());
                        $request->attributes->set('q', $category3->getName());

                        $showCatalog = false;
                        $seoCategories[] = $category3->getName();
                    }

                    if (!$categories3) {
                        $showCatalog = false;
                    }
                }
            }
        }

        $slugs = [
            'locality'  => $localitySlug,
            'category1' => $categorySlug1,
            'category2' => $categorySlug2,
            'category3' => $categorySlug3,
        ];

        $entities = [
            'locality'  => $locality,
            'category1' => $category1,
            'category2' => $category2,
            'category3' => $category3,
        ];

        if (!$searchManager->checkCatalogRedirect($slugs, $entities)) {
            return $this->handlePermanentRedirect($locality, $category1, $category2, $category3);
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
            ]
        );

        if (!$locality) {
            $locationMarkers = $this->getBusinessProfileManager()->getLocationMarkersFromLocalityData($localities);
        } elseif (!$category1) {
            $locationMarkers = $this->getBusinessProfileManager()->getLocationMarkersFromLocalityData([$locality]);
        } else {
            $searchDTO = $searchManager->getSearchCatalogDTO($request, $locality, $category1, $category2, $category3);

            //locality lat and lan required
            if ($searchDTO) {
                $dcDataDTO = $searchManager->getDoubleClickCatalogData($searchDTO);
                $searchResultsDTO = $searchManager->searchCatalog($searchDTO, $locale);

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

        $catalogLevelItems = $searchManager->sortCatalogItems($localities, $categories1, $categories2, $categories3);

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
                'currentCategory1'   => $category1,
                'currentCategory2'   => $category2,
                'currentCategory3'   => $category3,
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
            'category2',
            'category3',
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

    private function handlePermanentRedirect($locality = null, $category1 = null, $category2 = null, $category3 = null)
    {
        $data = [
            'localitySlug'    => null,
            'categorySlug1'    => null,
            'categorySlug2'   => null,
            'categorySlug3'   => null,
        ];

        if ($locality) {
            if (!$category1->getParent()) {
                $data['categorySlug1'] = $category1->getSlug();

                if ($category2->getLvl() == Category::CATEGORY_LEVEL_2 and $category2->getParent() == $category1) {
                    $data['categorySlug2'] = $category2->getSlug();

                    if ($category3->getLvl() == Category::CATEGORY_LEVEL_3 and $category3->getParent() == $category2) {
                        $data['categorySlug3'] = $category3->getSlug();
                    } else {
                        $data = $this->getCategoryManager()->getCategoryParents($category3);
                    }
                } else {
                    $data = $this->getCategoryManager()->getCategoryParents($category2);
                }
            } else {
                $data = $this->getCategoryManager()->getCategoryParents($category1);
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
