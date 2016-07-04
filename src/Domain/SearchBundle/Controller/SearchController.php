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
        $query = $request->get('q', '');
        $location = $request->get('geo', 'San Juan');
        $page     = $request->get('page', 1);
        $total = 0;
        $limit = 30;

        $businessProfilehManager = $this->get('domain_business.manager.business_profile');
        $categoryManager         = $this->get('domain_business.manager.category');
        $bannerFactory  = $this->get('domain_banner.factory.banner'); // Maybe need to load via factory, not manager

        $results       = $businessProfilehManager->searchByPhraseAndLocation($query, $location);

        $categories    = $categoryManager->getCategoriesByProfiles($results);
        $banner        = $bannerFactory->get(TypeInterface::CODE_PORTAL_LEADERBOARD);

        return $this->render('DomainSearchBundle:Search:index.html.twig', array(
            'results'       => $results,
            'query'         => $query,
            'page'          => $page,
            'total'         => $total,
            'limit'         => $limit,
            'location'      => $location,
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
        $page     = $request->get('page', 1);

        $businessProfilehManager = $this->get('domain_business.manager.business_profile');
        $categoryManager         = $this->get('domain_business.manager.category');

        $results            = $businessProfilehManager->searchWithMapByPhraseAndLocation($query, $location);

        $locationMarkers    = $businessProfilehManager->getLocationMarkersFromProfileData($results);
        $categories         = $categoryManager->getCategoriesByProfiles($results);

        return $this->render('DomainSearchBundle:Search:map.html.twig', array(
            'results'    => $results,
            'markers'    => $locationMarkers,
            'categories' => $categories
        ));
    }
}
