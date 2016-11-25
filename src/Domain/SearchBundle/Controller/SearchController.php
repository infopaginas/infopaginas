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

        $category    = false;
        $subcategory = false;

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

            if ($categorySlug) {
                $category = $searchManager->searchCatalogCategory($categorySlug);

                if ($category) {
                    $request->attributes->set('category', $category->getName());
                    $request->attributes->set('q', $category->getName());

                    $subcategories = $searchManager->searchSubcategoryByCategory($category, $request->getLocale());
                    $subcategory   = $searchManager->searchCatalogCategory($subcategorySlug);

                    if ($subcategory) {
                        $request->attributes->set('subcategory', $subcategory->getName());
                        $request->attributes->set('q', $subcategory->getName());
                    }
                }
            }
        }

        $searchResultsDTO = null;
        $dcDataDTO        = null;
        $schema = false;

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
        }

        // hardcode for catalog
        $pageRouter = 'domain_search_index';

        return $this->render(
            ':redesign:catalog.html.twig',
            [
                'search'        => $searchDTO,
                'results'       => $searchResultsDTO,
                'bannerFactory' => $bannerFactory,
                'dcDataDTO'     => $dcDataDTO,
                'searchData'    => $searchData,
                'pageRouter'    => $pageRouter,
                'localities'    => $localities,
                'categories'    => $categories,
                'subcategories' => $subcategories,
                'currentLocality' => $locality,
                'currentCategory' => $category,
                'currentSubcategory' => $subcategory,
                'schemaJsonLD' => $schema,
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
}
