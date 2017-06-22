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

        $path = explode('/', $uri);

        switch ($path[0]) {
            case self::REDIRECT_PREFIX_BUSINESS:
                // business page redirect
                $data = $this->getBusinessRedirectData($path);
                break;
            case self::REDIRECT_PREFIX_CATALOG:
                // catalog redirect
                $data = $this->getCatalogRedirectData($path);
                break;
            default:
                // redirect to search page
                $data = $this->getDefaultRedirectData($path);
                break;
        }

        return $this->redirectToRoute(
            $data['route'],
            $data['params'],
            301
        );
    }

    /**
     * @param array $path
     *
     * @return array
     */
    protected function getBusinessRedirectData($path)
    {
        end($path);
        $business   = prev($path);
        $locality   = prev($path);

        return [
            'params' => [
                'citySlug' => $locality,
                'slug'     => $business,
            ],
            'route' => 'domain_business_profile_view',
        ];
    }

    /**
     * @param array $path
     *
     * @return array
     */
    protected function getCatalogRedirectData($path)
    {
        end($path);
        $category   = prev($path);
        $locality   = prev($path);

        return [
            'params' => [
                'localitySlug' => $locality,
                'categorySlug' => $category,
            ],
            'route' => 'domain_search_catalog',
        ];
    }

    /**
     * @param array $path
     *
     * @return array
     */
    protected function getDefaultRedirectData($path)
    {
        return [
            'params' => [
                'q' => implode(' ', $path),
            ],
            'route' => 'domain_search_index',
        ];
    }
}
