<?php

namespace Domain\SearchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class SearchController
 * @package Domain\SearchBundle\Controller
 */
class SearchController extends Controller
{
    /**
     * Main Search page
     */
    public function indexAction()
    {
        return $this->render('DomainSiteBundle:Home:search.html.twig');
    }

    /**
     * Search by category
     */
    public function categoryAction()
    {
        return $this->render('DomainSiteBundle:Home:search.html.twig');
    }


    /**
     * Source endpoint for jQuery UI Autocomplete plugin in search widget
     */
    public function autocompleteAction(Request $request)
    {
        $term = $request->get('term', '');
        return (new JsonResponse)->setData(
            array($term)
        );
    }
}
