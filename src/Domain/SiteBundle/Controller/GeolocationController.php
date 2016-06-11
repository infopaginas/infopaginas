<?php

namespace Domain\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class HomeController
 * @package Domain\SiteBundle\Controller
 */
class GeolocationController extends Controller
{
    /**
     * Source endpoint for jQuery UI Autocomplete plugin in geolocation widget
     */
    public function autocompleteAction(Request $request)
    {
        $term = $request->get('term', '');
        return (new JsonResponse)->setData(
            array($term)
        );
    }
}
