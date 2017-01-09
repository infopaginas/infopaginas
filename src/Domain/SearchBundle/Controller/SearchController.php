<?php

namespace Domain\SearchBundle\Controller;

use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\BusinessBundle\Manager\CategoryManager;
use Domain\BusinessBundle\Manager\LocalityManager;
use Domain\ReportBundle\Manager\SearchLogManager;
use Domain\SearchBundle\Util\SearchDataUtil;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

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
        $seoCategories = [];

        if ($searchDTO) {
            $searchResultsDTO = $searchManager->search($searchDTO, $locale);
            $dcDataDTO        = $searchManager->getDoubleClickData($searchDTO);

            $this->getBusinessProfileManager()
                ->trackBusinessProfilesCollectionImpressions($searchResultsDTO->resultSet);
            $this->getSearchLogManager()
                ->saveProfilesDataSuggestedBySearchQuery($searchData['q'], $searchResultsDTO->resultSet);

            $schema = $this->getBusinessProfileManager()->buildBusinessProfilesSchema($searchResultsDTO->resultSet);

            $locationMarkers = $this->getBusinessProfileManager()
                ->getLocationMarkersFromProfileData($searchResultsDTO->resultSet);

            if ($searchDTO->locationValue) {
                $locationName = $searchDTO->locationValue->name;
            }

            if ($searchDTO->query) {
                $seoCategories[] = $searchDTO->query;
            }
        } else {
            $searchResultsDTO = null;
            $dcDataDTO        = null;
            $locationMarkers  = $this->getBusinessProfileManager()->getDefaultLocationMarkers();
        }

        $bannerFactory = $this->get('domain_banner.factory.banner');
        $bannerFactory->prepearBanners(
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
            ]
        );
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

    public function mapAction(Request $request)
    {
//        todo remove this
        $searchManager = $this->get('domain_search.manager.search');

        $searchDTO = $searchManager->getSearchDTO($request);

        $searchData = $this->getSearchDataByRequest($request);

        $locale = ucwords($request->getLocale());

        if ($searchDTO) {
            $searchResultsDTO   = $searchManager->search($searchDTO, $locale);

            $businessProfileManager = $this->get('domain_business.manager.business_profile');

            $searchResultsDTO   = $businessProfileManager->removeItemWithHiddenAddress($searchResultsDTO);
            $locationMarkers    = $businessProfileManager->getLocationMarkersFromProfileData($searchResultsDTO->resultSet);

            $this->getBusinessProfileManager()
                ->trackBusinessProfilesCollectionImpressions($searchResultsDTO->resultSet);

            $this->getSearchLogManager()
                ->saveProfilesDataSuggestedBySearchQuery($searchData['q'], $searchResultsDTO->resultSet);

            $schema = $this->getBusinessProfileManager()->buildBusinessProfilesSchema($searchResultsDTO->resultSet);
        } else {
            $searchResultsDTO = null;
            $locationMarkers = null;
        }

        $bannerFactory  = $this->get('domain_banner.factory.banner');
        $bannerFactory->prepearBanners(array(
            TypeInterface::CODE_PORTAL
        ));

        $pageRouter = $this->container->get('request')->attributes->get('_route');

        return $this->render(
            'DomainSearchBundle:Search:map.html.twig',
            [
                'results'       => $searchResultsDTO,
                'markers'       => $locationMarkers,
                'bannerFactory' => $bannerFactory,
                'searchData'    => $searchData,
                'pageRouter'    => $pageRouter,
                'schemaJsonLD'  => $schema,
            ]
        );
    }

    public function compareAction(Request $request)
    {
        $searchManager = $this->get('domain_search.manager.search');

        $searchDTO = $searchManager->getSearchDTO($request);

        $searchData = $this->getSearchDataByRequest($request);

        $locale = ucwords($request->getLocale());

        $locationName = false;
        $seoCategories = [];

        if ($searchDTO) {
            $searchResultsDTO   = $searchManager->search($searchDTO, $locale);

            $this->getBusinessProfileManager()
                ->trackBusinessProfilesCollectionImpressions($searchResultsDTO->resultSet);

            $this->getSearchLogManager()
                ->saveProfilesDataSuggestedBySearchQuery($searchData['q'], $searchResultsDTO->resultSet);

            $schema = $this->getBusinessProfileManager()->buildBusinessProfilesSchema($searchResultsDTO->resultSet);

            if ($searchDTO->locationValue) {
                $locationName = $searchDTO->locationValue->name;
            }

            if ($searchDTO->query) {
                $seoCategories[] = $searchDTO->query;
            }
        } else {
            $searchResultsDTO = null;
            $schema           = null;
        }

        $bannerFactory  = $this->get('domain_banner.factory.banner');
        $bannerFactory->prepearBanners(
            [
                TypeInterface::CODE_SEARCH_PAGE_BOTTOM,
                TypeInterface::CODE_SEARCH_PAGE_TOP,
            ]
        );

        $pageRouter = $this->container->get('request')->attributes->get('_route');

        $seoData = $this->getBusinessProfileManager()->getBusinessProfileSearchSeoData($locationName, $seoCategories);

        return $this->render(
            ':redesign:search-results-compare.html.twig',
            [
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

    /**
     * @return \Domain\ReportBundle\Manager\SearchLogManager
     */
    protected function getSearchLogManager() : SearchLogManager
    {
        return $this->get('domain_report.manager.search_log');
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

        $localities = $this->getLocalityManager()->findAll();

        $locality = $searchManager->searchCatalogLocality($localitySlug);

        $request->attributes->set('geo', $localitySlug);
        $request->attributes->set('q', $localitySlug);

        if ($locality) {
            $categories1 = $this->getCategoryManager()->getAvailableParentCategories($request->getLocale());

            $request->attributes->set('catalogLocality', $locality->getName());
            $request->attributes->set('geo', $locality->getName());
            $request->attributes->set('q', $locality->getName());

            $category1 = $searchManager->searchCatalogCategory($categorySlug1);
            $seoLocationName = $locality->getName();

            if ($category1 and !$category1->getParent()) {
                $request->attributes->set('category', $category1->getName());
                $request->attributes->set('q', $category1->getName());

                $categories2 = $searchManager->searchSubcategoryByCategory(
                    $category1,
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

                    $categories3 = $searchManager->searchSubcategoryByCategory(
                        $category2,
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
        $bannerFactory->prepearBanners(
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
                $dcDataDTO = $searchManager->getDoubleClickData($searchDTO);
                $searchResultsDTO = $searchManager->searchCatalog($searchDTO, $locale);

                $this->getBusinessProfileManager()
                    ->trackBusinessProfilesCollectionImpressions($searchResultsDTO->resultSet);

                $this->getSearchLogManager()
                    ->saveProfilesDataSuggestedBySearchQuery($searchData['q'], $searchResultsDTO->resultSet);

                $schema = $this->getBusinessProfileManager()->buildBusinessProfilesSchema($searchResultsDTO->resultSet);

                $locationMarkers = $this->getBusinessProfileManager()
                    ->getLocationMarkersFromProfileData($searchResultsDTO->resultSet);
            }
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
