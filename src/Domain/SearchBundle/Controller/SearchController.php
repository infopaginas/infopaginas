<?php

namespace Domain\SearchBundle\Controller;

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

        $searchDTO          = $searchManager->getSearchDTO($request);
        $searchResultsDTO   = $searchManager->search($searchDTO);

        // $query = $request->get('q', '');
        // $categoryFilter = $request->get('category', null);
        // $page     = $request->get('page', 1);

        // $geolocationManager = $this->get('oxa_geolocation.manager');
        // $locationValue      = $geolocationManager->buildLocationValueFromRequest($request);

        $total = 145;
        $limit = 20;

        $businessProfilehManager = $this->get('domain_business.manager.business_profile');
        $categoryManager         = $this->get('domain_business.manager.category');
        $bannerFactory           = $this->get('domain_banner.factory.banner'); // Maybe need to load via factory, not manager

        $results       = $businessProfilehManager->searchByPhraseAndLocation($query, $locationValue, $categoryFilter);

        $categories    = $categoryManager->getCategoriesByProfiles();
        $banner        = $bannerFactory->get(TypeInterface::CODE_PORTAL_LEADERBOARD);

        return $this->render('DomainSearchBundle:Search:index.html.twig', array(
            'results'       => $results,
            'query'         => $query,
            'page'          => $page,
            'total'         => $total,
            'limit'         => $limit,
            'location'      => $locationValue->name,
            'banner'        => $banner,
            'categories'    => $categories
        ));
    }

    /**
     * Search by category
     */
    public function categoryAction(Request $request)
    {
        return $this->render('DomainSearchBundle:Home:search.html.twig');
    }


    /**
     * Source endpoint for jQuery UI Autocomplete plugin in search widget
     */
    public function autocompleteAction(Request $request)
    {
        $query = $request->get('term', '');
        $location = $request->get('geo', '');

        $businessProfilehManager = $this->get('domain_business.manager.business_profile');
        $results = $businessProfilehManager->searchAutosuggestByPhraseAndLocation($query, $location);

        return (new JsonResponse)->setData($results);
    }

    public function mapAction(Request $request)
    {
        $query = $request->get('q', '');
        $location = $request->get('geo', 'San Juan');
        $categoryFilter = $request->get('category', null);
        $page     = $request->get('page', 1);

        $businessProfilehManager = $this->get('domain_business.manager.business_profile');
        $categoryManager         = $this->get('domain_business.manager.category');

        $results            = $businessProfilehManager->searchWithMapByPhraseAndLocation($query, $location, $categoryFilter);

        $locationMarkers    = $businessProfilehManager->getLocationMarkersFromProfileData($results);
        $categories         = $categoryManager->getCategoriesByProfiles(array_column($results, 0));

        return $this->render('DomainSearchBundle:Search:map.html.twig', array(
            'results'    => $results,
            'markers'    => $locationMarkers,
            'categories' => $categories
        ));
    }

    public function compareAction(Request $request)
    {
        $query = $request->get('q', '');
        $categoryFilter = $request->get('category', null);
        $page     = $request->get('page', 1);

        $geolocationManager = $this->get('oxa_geolocation.manager');
        $locationValue      = $geolocationManager->buildLocationValueFromRequest($request);

        $total = 0;
        $limit = 20;

        $businessProfilehManager = $this->get('domain_business.manager.business_profile');
        $categoryManager         = $this->get('domain_business.manager.category');
        $bannerFactory           = $this->get('domain_banner.factory.banner'); // Maybe need to load via factory, not manager

        $results       = $businessProfilehManager->searchByPhraseAndLocation($query, $locationValue, $categoryFilter);

        $results       = array_column($results, 0);
        $categories    = $categoryManager->getCategoriesByProfiles($results);
        $banner        = $bannerFactory->get(TypeInterface::CODE_PORTAL_LEADERBOARD);

        return $this->render('DomainSearchBundle:Search:compare.html.twig', array(
            'results'       => $results,
            'query'         => $query,
            'page'          => $page,
            'total'         => $total,
            'limit'         => $limit,
            'location'      => $locationValue->name,
            'banner'        => $banner,
            'categories'    => $categories
        ));
    }
}
