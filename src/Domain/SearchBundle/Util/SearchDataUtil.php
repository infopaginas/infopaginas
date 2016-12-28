<?php

namespace Domain\SearchBundle\Util;

use Domain\SearchBundle\Model\DataType\SearchDTO;
use Domain\SearchBundle\Model\DataType\SearchResultsDTO;
use Symfony\Component\HttpFoundation\Request;
use Oxa\GeolocationBundle\Model\Geolocation\LocationValueObject;

class SearchDataUtil
{
    const ORDER_BY_RELEVANCE        = 'relevance';
    const ORDER_BY_DISTANCE         = 'distance';
    const DEFAULT_ORDER_BY_VALUE    = 'relevance';

    public static function buildResponceDTO(
        $resutlSet,
        int $totalCount,
        int $page,
        int $pageCount,
        array $categories,
        $neighborhoods
    ) : SearchResultsDTO {
        return new SearchResultsDTO($resutlSet, $totalCount, $page, $pageCount, $categories, $neighborhoods);
    }

    public static function buildRequestDTO($query, LocationValueObject $location, int $page, int $limit) : SearchDTO
    {
        return new SearchDTO($query, $location, $page, $limit);
    }

    public static function getQueryFromRequest(Request $request)
    {
        return $request->get('q', null);
    }

    public static function getPageFromRequest(Request $request) : int
    {
        return $request->get('page', 1);
    }

    public static function getCategoryFromRequest(Request $request)
    {
        return $request->get('category', null);
    }

    public static function getCategory2FromRequest(Request $request)
    {
        return $request->get('category2', null);
    }


    public static function getCategory3FromRequest(Request $request)
    {
        return $request->get('category3', null);
    }

    public static function getCatalogLocalityFromRequest(Request $request)
    {
        return $request->get('catalogLocality', null);
    }

    public static function getNeighborhoodFromRequest(Request $request)
    {
        return $request->get('neighborhood', null);
    }

    public static function getOrderByFromRequest(Request $request)
    {
        return $request->get('order', self::ORDER_BY_RELEVANCE);
    }
}
