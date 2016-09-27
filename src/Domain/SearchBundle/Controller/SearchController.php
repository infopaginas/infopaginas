<?php

namespace Domain\SearchBundle\Controller;

use Domain\BusinessBundle\Manager\BusinessProfileManager;
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

        if ($searchDTO) {
            $searchResultsDTO   = $searchManager->search($searchDTO);
            $dcDataDTO          = $searchManager->getDoubleClickData($searchDTO);

            $this->getBusinessProfileManager()
                ->trackBusinessProfilesCollectionImpressions($searchResultsDTO->resultSet);
            $this->getSearchLogManager()
                ->saveProfilesDataSuggestedBySearchQuery($searchData['q'], $searchResultsDTO->resultSet);
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
        $results = $businessProfileManager->searchAutosuggestByPhraseAndLocation($searchData['q']);

        return (new JsonResponse)->setData($results);
    }

    public function mapAction(Request $request)
    {
        $searchManager = $this->get('domain_search.manager.search');

        $searchDTO = $searchManager->getSearchDTO($request);

        $searchData = $this->getSearchDataByRequest($request);

        if ($searchDTO) {
            $searchResultsDTO   = $searchManager->search($searchDTO);

            $businessProfileManager = $this->get('domain_business.manager.business_profile');

            $searchResultsDTO   = $businessProfileManager->removeItemWithHiddenAddress($searchResultsDTO);
            $locationMarkers    = $businessProfileManager->getLocationMarkersFromProfileData($searchResultsDTO->resultSet);

            $this->getBusinessProfileManager()
                ->trackBusinessProfilesCollectionImpressions($searchResultsDTO->resultSet);

            $this->getSearchLogManager()
                ->saveProfilesDataSuggestedBySearchQuery($searchData['q'], $searchResultsDTO->resultSet);
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
            ]
        );
    }

    public function compareAction(Request $request)
    {
        $searchManager = $this->get('domain_search.manager.search');

        $searchDTO = $searchManager->getSearchDTO($request);

        $searchData = $this->getSearchDataByRequest($request);

        if ($searchDTO) {
            $searchResultsDTO   = $searchManager->search($searchDTO);

            $this->getBusinessProfileManager()
                ->trackBusinessProfilesCollectionImpressions($searchResultsDTO->resultSet);

            $this->getSearchLogManager()
                ->saveProfilesDataSuggestedBySearchQuery($searchData['q'], $searchResultsDTO->resultSet);
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

    public function catalogAction(Request $request, $citySlug = '', $categorySlug = '')
    {
        //todo - replace with slugs

        $q      = ucwords(str_replace('-', ' ', $categorySlug));
        $geo    = ucwords(str_replace('-', ' ', $citySlug));

        $request->attributes->set('q', $q);
        $request->attributes->set('geo', $geo);

        return $this->indexAction($request);
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
}
