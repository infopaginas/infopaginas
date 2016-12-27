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
        $query        = false;

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
                $query = $searchDTO->query;
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

        $seoData = $this->getBusinessProfileManager()->getBusinessProfileSearchSeoData($locationName, $query);

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
        $query        = false;

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
                $query = $searchDTO->query;
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

        $seoData = $this->getBusinessProfileManager()->getBusinessProfileSearchSeoData($locationName, $query);

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
        $categorySlug = '',
        $categorySlug2 = '',
        $categorySlug3 = ''
    ) {
        $searchManager = $this->get('domain_search.manager.search');

        $category    = null;
        $category2   = null;
        $category3   = null;
        $showResults = null;
        $showCatalog = true;

        $seoLocationName    = null;
        $seoCategoryName    = null;
        $seoSubcategoryName = null;

        $categories  = [];
        $categories2 = [];
        $categories3 = [];

        $localities = $this->getLocalityManager()->findAll();

        $locality = $searchManager->searchCatalogLocality($localitySlug);

        $request->attributes->set('geo', $localitySlug);
        $request->attributes->set('q', $localitySlug);

        if ($locality) {
            $categories = $this->getCategoryManager()->getAvailableParentCategories($request->getLocale());

            $request->attributes->set('catalogLocality', $locality->getName());
            $request->attributes->set('geo', $locality->getName());
            $request->attributes->set('q', $locality->getName());

            $category = $searchManager->searchCatalogCategory($categorySlug);
            $seoLocationName = $locality->getName();

            if ($category and !$category->getParent()) {
                $request->attributes->set('category', $category->getName());
                $request->attributes->set('q', $category->getName());

                $categories2 = $searchManager->searchSubcategoryByCategory(
                    $category,
                    Category::CATEGORY_LEVEL_2,
                    $request->getLocale()
                );
                $category2   = $searchManager->searchCatalogCategory($categorySlug2);

                $showResults = true;
                $seoCategoryName = $category->getName();

                if ($category2 and $category2->getParent()) {
                    $request->attributes->set('subcategory', $category2->getName());
                    $request->attributes->set('q', $category2->getName());

                    $categories3 = $searchManager->searchSubcategoryByCategory(
                        $category2,
                        Category::CATEGORY_LEVEL_3,
                        $request->getLocale()
                    );
                    $category3   = $searchManager->searchCatalogCategory($categorySlug3);

                    $seoSubcategoryName = $category2->getName();    //todo https://jira.oxagile.com/browse/INFT-312

                    if ($category3 and $category3->getParent()) {
                        $request->attributes->set('subcategory', $category3->getName());
                        $request->attributes->set('q', $category3->getName());

                        $showCatalog = false;
                        $seoSubcategoryName = $category3->getName();    //todo https://jira.oxagile.com/browse/INFT-312
                    }

                    if (!$categories3) {
                        $showCatalog = false;
                    }
                }
            }
        }

        $slugs = [
            'locality'  => $localitySlug,
            'category'  => $categorySlug,
            'category2' => $categorySlug2,
            'category3' => $categorySlug3,
        ];

        $entities = [
            'locality'  => $locality,
            'category'  => $category,
            'category2' => $category2,
            'category3' => $category3,
        ];

        if (!$searchManager->checkCatalogRedirect($slugs, $entities)) {
            return $this->handlePermanentRedirect($locality, $category, $category2, $category3);
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
        } elseif (!$category) {
            $locationMarkers = $this->getBusinessProfileManager()->getLocationMarkersFromLocalityData([$locality]);
        } else {
            $searchDTO = $searchManager->getSearchCatalogDTO($request, $locality, $category, $category2, $category3);

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

        $catalogLevelItems = $searchManager->sortCatalogItems($localities, $categories, $categories2, $categories3);

        $seoData = $this->getBusinessProfileManager()
            ->getBusinessProfileSearchSeoData($seoLocationName, $seoCategoryName, $seoSubcategoryName, true);

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

    private function handlePermanentRedirect($locality = null, $category = null, $category2 = null, $category3 = null)
    {
        $data = [
            'localitySlug'    => null,
            'categorySlug'    => null,
            'categorySlug2'   => null,
            'categorySlug3'   => null,
        ];

        if ($locality) {
            if (!$category->getParent()) {
                $data['categorySlug'] = $category->getSlug();

                if ($category2->getLvl() == Category::CATEGORY_LEVEL_2 and $category2->getParent() == $category) {
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
                $data = $this->getCategoryManager()->getCategoryParents($category);
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
