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

        $bannerFactory  = $this->get('domain_banner.factory.banner');
        $bannerFactory->prepearBanners(array(
            TypeInterface::CODE_PORTAL_LEADERBOARD,
            TypeInterface::CODE_PORTAL,
        ));

        return $this->render(
            'DomainSearchBundle:Search:index.html.twig',
            [
                'search'        => $searchDTO,
                'results'       => $searchResultsDTO,
                'bannerFactory' => $bannerFactory,
            ]
        );
    }

    /**
     * Source endpoint for jQuery UI Autocomplete plugin in search widget
     */
    public function autocompleteAction(Request $request)
    {
        $searchManager = $this->get('domain_search.manager.search');

        $searchDTO     = $searchManager->getSearchDTO($request);

        $businessProfilehManager = $this->get('domain_business.manager.business_profile');
        $results = $businessProfilehManager->searchAutosuggestByPhraseAndLocation($searchDTO);

        return (new JsonResponse)->setData($results);
    }

    public function mapAction(Request $request)
    {
        $searchManager = $this->get('domain_search.manager.search');

        $searchDTO          = $searchManager->getSearchDTO($request);
        $searchResultsDTO   = $searchManager->search($searchDTO);

        $businessProfileManager = $this->get('domain_business.manager.business_profile');
        $searchResultsDTO   = $businessProfileManager->removeItemWithHiddenAddress($searchResultsDTO);
        $locationMarkers    = $businessProfileManager->getLocationMarkersFromProfileData($searchResultsDTO->resultSet);

        $bannerFactory  = $this->get('domain_banner.factory.banner');
        $bannerFactory->prepearBanners(array(
            TypeInterface::CODE_PORTAL
        ));


        return $this->render(
            'DomainSearchBundle:Search:map.html.twig',
            [
                'results'    => $searchResultsDTO,
                'markers'    => $locationMarkers,
                'bannerFactory' => $bannerFactory,
            ]
        );
    }

    public function compareAction(Request $request)
    {
        $searchManager = $this->get('domain_search.manager.search');

        $searchDTO          = $searchManager->getSearchDTO($request);
        $searchResultsDTO   = $searchManager->search($searchDTO);

        $bannerFactory  = $this->get('domain_banner.factory.banner');
        $bannerFactory->prepearBanners(array(
            TypeInterface::CODE_PORTAL
        ));

        return $this->render(
            'DomainSearchBundle:Search:compare.html.twig',
            [
                'results'       => $searchResultsDTO,
                'bannerFactory' => $bannerFactory,
            ]
        );
    }
}
