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
        $locale = $request->getLocale();
        $term   = $request->get('term', '');

        $geolocationManager = $this->get('domain_site.manager.geolocation');
        $data = $geolocationManager->getGooglePlacesSuggestions($term, $locale);

        return (new JsonResponse)->setData($data);
    }
}
