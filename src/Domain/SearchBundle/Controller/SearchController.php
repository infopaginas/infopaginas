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
        $location = $request->get('loc', '');

        $searchManager = $this->get('domain_business.manager.business_profile');
        $bannerFactory  = $this->get('domain_banner.factory.banner'); // Maybe need to load via factory, not manager

        $results       = $searchManager->searchByPhraseAndLocation($query, $location);
        $banner         = $bannerFactory->get(TypeInterface::CODE_PORTAL_LEADERBOARD);

        return $this->render('DomainSiteBundle:Search:index.html.twig', array(
            'results' => $results,
            'banner'  => $banner
        ));
    }

    /**
     * Search by category
     */
    public function categoryAction(Request $request)
    {
        return $this->render('DomainSiteBundle:Home:search.html.twig');
    }


    /**
     * Source endpoint for jQuery UI Autocomplete plugin in search widget
     */
    public function autocompleteAction(Request $request)
    {
        $term = $request->get('term', '');
        $location = $request->get('loc', '');

        $searchManager = $this->get('domain_business.manager.business_profile');
        $results = $searchManager->searchAutosuggestByPhraseAndLocation($term, $location);

        return (new JsonResponse)->setData($results);
    }
}
