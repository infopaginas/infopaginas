<?php

namespace Domain\SearchBundle\Controller;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Entity\Locality;
use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\BusinessBundle\Manager\CategoryManager;
use Domain\BusinessBundle\Manager\ClickbaitTitleManager;
use Domain\BusinessBundle\Manager\LocalityManager;
use Domain\BusinessBundle\Util\BusinessProfileUtil;
use Domain\BusinessBundle\Util\SlugUtil;
use Domain\ReportBundle\Manager\KeywordsReportManager;
use Domain\ReportBundle\Model\BusinessOverviewModel;
use Domain\SearchBundle\Model\Manager\SearchManager;
use Domain\SearchBundle\Util\CacheUtil;
use Domain\SearchBundle\Util\SearchDataUtil;
use Domain\SiteBundle\Utils\Helpers\LocaleHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Domain\BannerBundle\Model\TypeInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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

        $schema       = false;
        $locationName = false;
        $closestLocality = '';
        $disableFilters = false;
        $seoCategories = [];
        $trackingParams = [];
        $seoType = BusinessProfileUtil::SEO_CLASS_PREFIX_SEARCH;
        $allowRedirect = !filter_var($request->get('redirected', false), FILTER_VALIDATE_BOOLEAN);

        if ($searchDTO) {
            if ($searchDTO->checkSearchInMap()) {
                $disableFilters = true;

                $closestLocality = $this->getBusinessProfileManager()->searchClosestLocalityInElastic($searchDTO);
                $seoType = BusinessProfileUtil::SEO_CLASS_PREFIX_SEARCH_MAP;
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

            $searchResultsDTO = $searchManager->search($searchDTO, $disableFilters);
            $dcDataDTO        = $searchManager->getDoubleClickData($searchDTO);

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
                $seoCategories[] = $searchDTO->getOriginalQuery();
            }

            $trackingParams = BusinessProfileUtil::getTrackingImpressionParamsData($searchResultsDTO->resultSet);
            $trackingParams = BusinessProfileUtil::getTrackingKeywordsParamsData(
                $searchData['q'],
                $searchResultsDTO->resultSet,
                $trackingParams
            );
        } else {
            return $this->forward(
                'DomainSiteBundle:Redirect:business',
                ['localitySlug' => $request->query->get('geo')]
            );
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

        $homepageCarouselManager = $this->container->get('domain_business.manager.homepage_carousel_manager');
        $carouselBusinesses = $homepageCarouselManager->getCarouselBusinessesSortedByRandom();
        $showCarousel = $homepageCarouselManager->isShowCarousel($carouselBusinesses);

        return $this->render(
            ':redesign:search-results.html.twig',
            [
                'search'              => $searchDTO,
                'results'             => $searchResultsDTO,
                'seoData'             => $seoData,
                'seoTags'             => BusinessProfileUtil::getSeoTags($seoType),
                'banners'             => $banners,
                'dcDataDTO'           => $dcDataDTO,
                'searchData'          => $searchData,
                'pageRouter'          => $pageRouter,
                'schemaJsonLD'        => $schema,
                'markers'             => $locationMarkers,
                'noFollowRelevance'   => SearchDataUtil::ORDER_BY_RELEVANCE != SearchDataUtil::DEFAULT_ORDER_BY_VALUE,
                'noFollowDistance'    => SearchDataUtil::ORDER_BY_DISTANCE  != SearchDataUtil::DEFAULT_ORDER_BY_VALUE,
                'searchRelevance'     => SearchDataUtil::ORDER_BY_RELEVANCE,
                'searchDistance'      => SearchDataUtil::ORDER_BY_DISTANCE,
                'disableFilters'      => $disableFilters,
                'trackingParams'      => $trackingParams,
                'carouselBusinesses'  => $carouselBusinesses,
                'showCarousel'        => $showCarousel,
            ]
        );
    }

    public function showDirectionsAction(string $slug)
    {
        /** @var BusinessProfile $businessProfile */
        $businessProfile = $this->getBusinessProfileManager()->findBySlug($slug);

        if (!$businessProfile) {
            $businessProfileAlias = $this->getBusinessProfileManager()->findByAlias($slug);

            if ($businessProfileAlias) {
                return $this->redirectToRoute(
                    'domain_search_show_directions',
                    ['slug' => $businessProfileAlias->getSlug()],
                    301
                );
            } else {
                throw new \Symfony\Component\HttpKernel\Exception\GoneHttpException();
            }
        } elseif (!$businessProfile->getIsActive()) {
            throw $this->createNotFoundException();
        }

        $targetCoordinates = $businessProfile->getLatitude() . ',' . $businessProfile->getLongitude();

        return $this->render(
            ':redesign:mapbox-get-directions.html.twig',
            ['targetCoordinates'  => $targetCoordinates]
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

        $data = [];
        /** @var Locality $closestLocality */
        $closestLocality = $this->getBusinessProfileManager()->searchClosestLocalityInElastic($searchDTO);

        if ($closestLocality) {
            $data = [
                'localityId' => $closestLocality->getId(),
                'localityName' => $closestLocality->getName(),
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * Source endpoint for jQuery UI Autocomplete plugin in search widget
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function autocompleteAction(Request $request): JsonResponse
    {
        $searchData = $this->getSearchDataByRequest($request);
        $locale = LocaleHelper::getLocale($request->getLocale());
        $memcached = $this->container->get('app.cache.memcached');
        $cacheId = CacheUtil::PREFIX_AUTOCOMPLETE . CacheUtil::sanitizeCacheId($searchData['q']) . $locale;
        $response = $memcached->fetch($cacheId);

        if (!$response) {
            $businessProfileManager = $this->get('domain_business.manager.business_profile');
            $results = $businessProfileManager->searchCategoryAndBusinessAutosuggestByPhrase(
                SearchManager::getSafeSearchString($searchData['q']),
                $locale
            );
            $response = (new JsonResponse())->setData($results);
            $memcached->save($cacheId, $response, CacheUtil::AUTOCOMPLETE_CACHE_LIFETIME);
        }

        return $response;
    }

    /**
     * autocomplete locality by name
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function autocompleteLocalityAction(Request $request): JsonResponse
    {
        $locale = LocaleHelper::getLocale($request->getLocale());
        $term = SearchManager::getSafeSearchString($request->get('term', ''));

        $businessProfileManager = $this->get('domain_business.manager.business_profile');
        $data = $businessProfileManager->searchLocalityAutoSuggestInElastic(
            $term,
            $locale,
            LocalityManager::AUTO_SUGGEST_MAX_LOCALITY_COUNT
        );

        $result = [];

        foreach ($data as $item) {
            $result[] = $item['name'];
        }

        return (new JsonResponse())->setData($result);
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

        $locationName = false;
        $closestLocality = '';
        $seoCategories = [];
        $trackingParams = [];
        $seoType = BusinessProfileUtil::SEO_CLASS_PREFIX_COMPARE;
        $locale = LocaleHelper::getLocale($request->getLocale());

        if ($searchDTO) {
            if ($searchDTO->checkSearchInMap()) {
                $closestLocality = $this->getBusinessProfileManager()->searchClosestLocalityInElastic($searchDTO);
                $seoType = BusinessProfileUtil::SEO_CLASS_PREFIX_COMPARE_MAP;
            }

            $searchResultsDTO = $searchManager->search($searchDTO);

            $schema = $this->getBusinessProfileManager()->buildBusinessProfilesSchema($searchResultsDTO->resultSet);

            if ($closestLocality) {
                $locationName = $closestLocality->getName();
                $searchData['geo'] = $locationName;
            } elseif ($searchDTO->locationValue) {
                $locationName = $searchDTO->locationValue->name;
            }

            if ($searchDTO->query) {
                $seoCategories[] = $searchDTO->getOriginalQuery();
            }

            $trackingParams = BusinessProfileUtil::getTrackingImpressionParamsData($searchResultsDTO->resultSet);
            $trackingParams = BusinessProfileUtil::getTrackingKeywordsParamsData(
                $searchData['q'],
                $searchResultsDTO->resultSet,
                $trackingParams
            );
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

        $pageRouter = $this->container->get('request_stack')->getCurrentRequest()->attributes->get('_route');

        $seoData = $this->getBusinessProfileManager()->getBusinessProfileSearchSeoData($locationName, $seoCategories);

        return $this->render(
            ':redesign:search-results-compare.html.twig',
            [
                'search'            => $searchDTO,
                'results'           => $searchResultsDTO,
                'seoData'           => $seoData,
                'seoTags'           => BusinessProfileUtil::getSeoTags($seoType),
                'banners'           => $banners,
                'searchData'        => $searchData,
                'pageRouter'        => $pageRouter,
                'schemaJsonLD'      => $schema,
                'noFollowRelevance' => SearchDataUtil::ORDER_BY_RELEVANCE != SearchDataUtil::DEFAULT_ORDER_BY_VALUE,
                'noFollowDistance'  => SearchDataUtil::ORDER_BY_DISTANCE  != SearchDataUtil::DEFAULT_ORDER_BY_VALUE,
                'searchRelevance'   => SearchDataUtil::ORDER_BY_RELEVANCE,
                'searchDistance'    => SearchDataUtil::ORDER_BY_DISTANCE,
                'locale'            => $locale,
                'trackingParams'    => $trackingParams,
            ]
        );
    }

    /**
     * @param Request $request
     * @param string $localitySlug
     * @param string $categorySlug
     *
     * @return Response
     */
    public function compareCatalogAction(Request $request, $localitySlug, $categorySlug)
    {
        $searchManager = $this->get('domain_search.manager.search');

        $locality = $searchManager->searchCatalogLocality($localitySlug);
        $category = $searchManager->searchCatalogCategory($categorySlug);

        if (!$locality || !$category) {
            throw $this->createNotFoundException();
        }
        $searchDTO = $searchManager->getSearchCatalogDTO($request, $locality, $category);

        $searchData = [];
        $trackingParams = [];
        $seoType = BusinessProfileUtil::SEO_CLASS_PREFIX_COMPARE;
        $locale = LocaleHelper::getLocale($request->getLocale());
        $seoCategory = '';

        if ($searchDTO) {
            $searchResultsDTO = $searchManager->searchCatalog($searchDTO);

            $schema = $this->getBusinessProfileManager()->buildBusinessProfilesSchema($searchResultsDTO->resultSet);

            $seoCategory = $category->getName();

            $this->setRequestAttributes($request, $locality, $category);
            $searchData = $this->getSearchDataByRequest($request);

            $trackingParams = BusinessProfileUtil::getTrackingImpressionParamsData($searchResultsDTO->resultSet);
            $trackingParams = BusinessProfileUtil::getTrackingKeywordsParamsData(
                $searchData['q'],
                $searchResultsDTO->resultSet,
                $trackingParams
            );
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

        $pageRouter = $this->container->get('request_stack')->getCurrentRequest()->attributes->get('_route');

        $seoData = $this->getBusinessProfileManager()->getBusinessProfileCatalogSeoData($locality, $seoCategory);

        return $this->render(
            ':redesign:search-results-compare.html.twig',
            [
                'search'            => $searchDTO,
                'results'           => $searchResultsDTO,
                'seoData'           => $seoData,
                'seoTags'           => BusinessProfileUtil::getSeoTags($seoType),
                'banners'           => $banners,
                'searchData'        => $searchData,
                'pageRouter'        => $pageRouter,
                'schemaJsonLD'      => $schema,
                'noFollowRelevance' => SearchDataUtil::ORDER_BY_RELEVANCE != SearchDataUtil::DEFAULT_ORDER_BY_VALUE,
                'noFollowDistance'  => SearchDataUtil::ORDER_BY_DISTANCE  != SearchDataUtil::DEFAULT_ORDER_BY_VALUE,
                'searchRelevance'   => SearchDataUtil::ORDER_BY_RELEVANCE,
                'searchDistance'    => SearchDataUtil::ORDER_BY_DISTANCE,
                'locale'            => $locale,
                'trackingParams'    => $trackingParams,
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

        $locationName  = '';
        $seoCategories = [];
        $trackingParams = [];
        $seoType = BusinessProfileUtil::SEO_CLASS_PREFIX_SEARCH_MAP;

        if ($searchDTO) {
            $closestLocality = $this->getBusinessProfileManager()->searchClosestLocalityInElastic($searchDTO);

            if ($closestLocality) {
                $searchDTO->locationValue->name = $closestLocality->getName();
                $searchDTO->locationValue->lat  = $closestLocality->getLatitude();
                $searchDTO->locationValue->lng  = $closestLocality->getLongitude();
                $searchDTO->locationValue->locality = $closestLocality;
            }

            $searchResultsDTO = $searchManager->search($searchDTO, true);
            $dcDataDTO        = $searchManager->getDoubleClickData($searchDTO);

            $locationMarkers = $this->getBusinessProfileManager()
                ->getLocationMarkersFromProfileData($searchResultsDTO->resultSet);

            if ($closestLocality) {
                $locationName = $closestLocality->getName();
                $request->query->set('geo', $locationName);
            }

            if ($searchDTO->query) {
                $seoCategories[] = $searchDTO->getOriginalQuery();
            }

            $trackingParams = BusinessProfileUtil::getTrackingImpressionParamsData($searchResultsDTO->resultSet);
            $trackingParams = BusinessProfileUtil::getTrackingKeywordsParamsData(
                $searchData['q'],
                $searchResultsDTO->resultSet,
                $trackingParams
            );
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
            'search'  => $searchDTO,
            'seoData' => $seoData,
            'seoTags' => BusinessProfileUtil::getSeoTags($seoType),
            'results' => $searchResultsDTO,
            'banners' => $banners,
        ];

        $html = $this->renderView(
            ':redesign/blocks:search_result_item_ajax.html.twig',
            $data
        );

        $router = $this->get('router');

        $staticSearchUrl  = $router->generate('domain_search_index', $request->query->all(), UrlGeneratorInterface::ABSOLUTE_PATH);
        $staticCompareUrl = $router->generate('domain_search_compare', $request->query->all(), UrlGeneratorInterface::ABSOLUTE_PATH);

        return new JsonResponse(
            [
                'html'      => $html,
                'seoData'   => $seoData,
                'markers'   => $locationMarkers,
                'targeting' => $dcDataDTO,
                'location'  => $locationName,
                'staticSearchUrl'  => $staticSearchUrl,
                'staticCompareUrl' => $staticCompareUrl,
                'trackingParams'   => $trackingParams,
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
     * @return ClickbaitTitleManager
     */
    protected function getClickbaitTitleManager() : ClickbaitTitleManager
    {
        return $this->get('domain_business.manager.clickbait_title_manager');
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
        $locale = LocaleHelper::getLocale($request->getLocale());

        $category   = null;
        $showResults = false;
        $showCatalog = true;
        $allowRedirect = !filter_var($request->get('redirected', false), FILTER_VALIDATE_BOOLEAN);

        $seoLocation = null;
        $seoCategory = '';
        $seoType = BusinessProfileUtil::SEO_CLASS_PREFIX_CATALOG;

        $categories = [];
        $localities = [];
        $trackingParams = [];

        $locality = $searchManager->searchCatalogLocality($localitySlug);

        $request->attributes->set('geo', $localitySlug);
        $request->attributes->set('q', $localitySlug);

        if ($locality) {
            $category = $searchManager->searchCatalogCategory($categorySlug);

            $this->setRequestAttributes($request, $locality, $category);
            $seoLocation = $locality;

            if ($category) {
                $showCatalog = false;

                $showResults = true;
                $seoCategory = $category->getName();
            } else {
                $categories = $this->getCategoryManager()->getAvailableCategoriesWithContent($locality, $locale);
            }
        } else {
            $localities = $this->getLocalityManager()->getCatalogLocalitiesWithContent($locale);
        }

        $slugs = [
            'locality' => $localitySlug,
            'category' => $categorySlug,
        ];

        $entities = [
            'locality' => $locality,
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

                $schema = $this->getBusinessProfileManager()->buildBusinessProfilesSchema($searchResultsDTO->resultSet);

                $locationMarkers = $this->getBusinessProfileManager()
                    ->getLocationMarkersFromProfileData($searchResultsDTO->resultSet);

                $trackingParams = BusinessProfileUtil::getTrackingImpressionParamsData($searchResultsDTO->resultSet);
                $trackingParams = BusinessProfileUtil::getTrackingKeywordsParamsData(
                    $searchData['q'],
                    $searchResultsDTO->resultSet,
                    $trackingParams
                );
                $trackingParams = BusinessProfileUtil::getTrackingCategoriesParamsData(
                    BusinessOverviewModel::TYPE_CODE_CATEGORY_CATALOG,
                    [$category],
                    [$locality],
                    $trackingParams
                );
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

        $seoData = $this->getBusinessProfileManager()->getBusinessProfileCatalogSeoData($seoLocation, $seoCategory);

        $pageRouter = 'domain_search_catalog';
        $searchData['localitySlug'] = $localitySlug;
        $searchData['categorySlug'] = $categorySlug;

        $homepageCarouselManager = $this->container->get('domain_business.manager.homepage_carousel_manager');
        $carouselBusinesses = $homepageCarouselManager->getCarouselBusinessesSortedByRandom();
        $showCarousel = $homepageCarouselManager->isShowCarousel($carouselBusinesses);

        return $this->render(
            ':redesign:catalog.html.twig',
            [
                'search'            => $searchDTO,
                'results'           => $searchResultsDTO,
                'seoData'           => $seoData,
                'seoTags'           => BusinessProfileUtil::getSeoTags($seoType),
                'banners'           => $banners,
                'dcDataDTO'         => $dcDataDTO,
                'searchData'        => $searchData,
                'pageRouter'        => $pageRouter,
                'currentLocality'   => $locality,
                'currentCategory'   => $category,
                'schemaJsonLD'      => $schema,
                'markers'           => $locationMarkers,
                'catalogLevelItems' => $catalogLevelItems,
                'showResults'       => $showResults,
                'showCatalog'       => $showCatalog,
                'noFollowRelevance' => SearchDataUtil::ORDER_BY_RELEVANCE != SearchDataUtil::DEFAULT_ORDER_BY_VALUE,
                'noFollowDistance'  => SearchDataUtil::ORDER_BY_DISTANCE  != SearchDataUtil::DEFAULT_ORDER_BY_VALUE,
                'searchRelevance'   => SearchDataUtil::ORDER_BY_RELEVANCE,
                'searchDistance'    => SearchDataUtil::ORDER_BY_DISTANCE,
                'trackingParams'    => $trackingParams,
                'clickbaitTitle'    => $this->getClickbaitTitleManager()->getClickbaitTitleByLocality($locality),
                'carouselBusinesses' => $carouselBusinesses,
                'showCarousel' => $showCarousel,
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

    /**
     * @param Request $request
     * @param Locality|null $locality
     * @param Category|null $category
     */
    private function setRequestAttributes(Request $request, Locality $locality = null, Category $category = null)
    {
        if ($locality) {
            $request->attributes->set('catalogLocality', $locality->getName());
            $request->attributes->set('geo', $locality->getName());
            $request->attributes->set('q', $locality->getName());
            if ($category) {
                $request->attributes->set('q', $category->getName());
            }
        }
    }
}
