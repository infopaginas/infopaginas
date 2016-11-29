<?php

namespace Domain\SearchBundle\Controller;

use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\BusinessBundle\Manager\CategoryManager;
use Domain\BusinessBundle\Manager\LocalityManager;
use Domain\ReportBundle\Manager\SearchLogManager;
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

        $schema = false;

        if ($searchDTO) {
            $searchResultsDTO   = $searchManager->search($searchDTO, $locale);
            $dcDataDTO          = $searchManager->getDoubleClickData($searchDTO);

            $this->getBusinessProfileManager()
                ->trackBusinessProfilesCollectionImpressions($searchResultsDTO->resultSet);
            $this->getSearchLogManager()
                ->saveProfilesDataSuggestedBySearchQuery($searchData['q'], $searchResultsDTO->resultSet);

            $schema = $this->getBusinessProfileManager()->buildBusinessProfilesSchema($searchResultsDTO->resultSet);
        } else {
            $searchResultsDTO = null;
            $dcDataDTO = null;
        }

        $bannerFactory = $this->get('domain_banner.factory.banner');
        $bannerFactory->prepearBanners(
            [
                TypeInterface::CODE_PORTAL_LEADERBOARD,
                TypeInterface::CODE_PORTAL,
            ]
        );

        // hardcode for catalog
        $pageRouter = 'domain_search_index';

        return $this->render(
            'DomainSearchBundle:Search:index.html.twig',
            [
                'search'        => $searchDTO,
                'results'       => $searchResultsDTO,
                'bannerFactory' => $bannerFactory,
                'dcDataDTO'     => $dcDataDTO,
                'searchData'    => $searchData,
                'pageRouter'    => $pageRouter,
                'schemaJsonLD'  => $schema,
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

        if ($searchDTO) {
            $searchResultsDTO   = $searchManager->search($searchDTO, $locale);

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
            'DomainSearchBundle:Search:compare.html.twig',
            [
                'results'       => $searchResultsDTO,
                'bannerFactory' => $bannerFactory,
                'searchData'    => $searchData,
                'pageRouter'    => $pageRouter,
                'schemaJsonLD'  => $schema,
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
        $subcategorySlug = ''
    ) {
        $searchManager = $this->get('domain_search.manager.search');

        $category    = null;
        $subcategory = null;

        $categories    = [];
        $subcategories = [];

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

            if ($category and !$category->getParent()) {
                $request->attributes->set('category', $category->getName());
                $request->attributes->set('q', $category->getName());

                $subcategories = $searchManager->searchSubcategoryByCategory($category, $request->getLocale());
                $subcategory   = $searchManager->searchCatalogCategory($subcategorySlug);

                if ($subcategory and $subcategory->getParent()) {
                    $request->attributes->set('subcategory', $subcategory->getName());
                    $request->attributes->set('q', $subcategory->getName());
                }
            }
        }

        if (!($this->checkSlug($localitySlug, $locality) and
            $this->checkSlug($categorySlug, $category) and
            $this->checkSlug($subcategorySlug, $subcategory) and
            $this->checkCategory($category) and
            $this->checkSubcategory($subcategory))) {

            return $this->handlePermanentRedirect($locality, $category, $subcategory);
        }

        $searchResultsDTO = null;
        $dcDataDTO        = null;
        $schema           = null;
        $locationMarkers  = null;

        $searchData = $this->getSearchDataByRequest($request);

        $locale = ucwords($request->getLocale());

        $bannerFactory = $this->get('domain_banner.factory.banner');
        $bannerFactory->prepearBanners(
            [
                TypeInterface::CODE_PORTAL_LEADERBOARD,
                TypeInterface::CODE_PORTAL,
            ]
        );

        $searchDTO = $searchManager->getSearchCatalogDTO($request, $locality, $category, $subcategory);

        //locality lat and lan required
        if ($searchDTO) {
            $dcDataDTO = $searchManager->getDoubleClickData($searchDTO);
            $searchResultsDTO = $searchManager->searchCatalog($searchDTO, $locale);

            $this->getBusinessProfileManager()
                ->trackBusinessProfilesCollectionImpressions($searchResultsDTO->resultSet);

            $this->getSearchLogManager()
                ->saveProfilesDataSuggestedBySearchQuery($searchData['q'], $searchResultsDTO->resultSet);

            $schema = $this->getBusinessProfileManager()->buildBusinessProfilesSchema($searchResultsDTO->resultSet);

            $locationMarkers = $this->getBusinessProfileManager()->getLocationMarkersFromProfileData($searchResultsDTO->resultSet);
        }

        $catalogLevelItems = $searchManager->sortCatalogItems($localities, $categories, $subcategories);

        // hardcode for catalog
        $pageRouter = 'domain_search_index';

        return $this->render(
            ':redesign:catalog.html.twig',
            [
                'search'             => $searchDTO,
                'results'            => $searchResultsDTO,
                'bannerFactory'      => $bannerFactory,
                'dcDataDTO'          => $dcDataDTO,
                'searchData'         => $searchData,
                'pageRouter'         => $pageRouter,
                'currentLocality'    => $locality,
                'currentCategory'    => $category,
                'currentSubcategory' => $subcategory,
                'schemaJsonLD'       => $schema,
                'markers'            => $locationMarkers,
                'catalogLevelItems'  => $catalogLevelItems,
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
            'subcategory',
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

    private function handlePermanentRedirect($locality = null, $category = null, $subcategory = null)
    {
        $localitySlug    = null;
        $categorySlug    = null;
        $subcategorySlug = null;

        if ($locality) {
            $localitySlug = $locality->getSlug();

            if ($category) {
                $parentCategory = $category->getParent();

                if ($parentCategory) {
                    $categorySlug    = $parentCategory->getSlug();
                    $subcategorySlug = $category->getSlug();
                } else {
                    $categorySlug    = $category->getSlug();

                    if ($subcategory and $subcategory->getParent()) {
                        $subcategorySlug = $subcategory->getSlug();
                    }
                }
            }
        }

        return $this->redirectToRoute(
            'domain_search_catalog',
            [
                'localitySlug'    => $localitySlug,
                'categorySlug'    => $categorySlug,
                'subcategorySlug' => $subcategorySlug,
            ],
            301
        );
    }

    private function checkCategory($category)
    {
        return !($category and $category->getParent());
    }

    private function checkSubcategory($subcategory)
    {
        return !($subcategory and !$subcategory->getParent());
    }

    private function checkSlug($requestSlug, $entity)
    {
        if ($requestSlug) {
            if ($entity and $entity->getSlug() == $requestSlug) {
                return true;
            } else {
                return false;
            }
        }

        return true;
    }
}
