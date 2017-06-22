<?php

namespace Domain\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class RedirectController extends Controller
{
    const REDIRECT_PREFIX_BUSINESS = 'page';
    const REDIRECT_PREFIX_CATALOG  = 'business';

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function indexAction(Request $request)
    {
        $uri = $request->attributes->get('path');

        if (strpos($uri, 'google') === 0 and file_exists($uri . '.html')) {
            // for google domain verification
            include($uri . '.html');
            die();
        }

        $pathParts = explode('/', $uri);

        switch ($pathParts[0]) {
            case self::REDIRECT_PREFIX_BUSINESS:
                // business page redirect
                $data = $this->getBusinessRedirectData($pathParts);
                break;
            case self::REDIRECT_PREFIX_CATALOG:
                // catalog redirect
                $data = $this->getCatalogRedirectData($pathParts);
                break;
            default:
                // redirect to search page
                $data = $this->getDefaultRedirectData($pathParts);
                break;
        }

        return $this->redirectToRoute(
            $data['route'],
            $data['params'],
            301
        );
    }

    /**
     * @param array $pathParts
     *
     * @return array
     */
    protected function getBusinessRedirectData($pathParts)
    {
        end($pathParts);
        $business   = prev($pathParts);
        $locality   = prev($pathParts);

        return [
            'params' => [
                'citySlug' => $locality,
                'slug'     => $business,
            ],
            'route' => 'domain_business_profile_view',
        ];
    }

    /**
     * @param array $pathParts
     *
     * @return array
     */
    protected function getCatalogRedirectData($pathParts)
    {
        end($pathParts);
        $category   = prev($pathParts);
        $locality   = prev($pathParts);

        return [
            'params' => [
                'localitySlug' => $locality,
                'categorySlug' => $category,
            ],
            'route' => 'domain_search_catalog',
        ];
    }

    /**
     * @param array $pathParts
     *
     * @return array
     */
    protected function getDefaultRedirectData($pathParts)
    {
        return [
            'params' => [
                'q' => implode(' ', $pathParts),
            ],
            'route' => 'domain_search_index',
        ];
    }
}
