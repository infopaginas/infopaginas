<?php

namespace Domain\SearchBundle\Util;

use Domain\SearchBundle\Model\DataType\SearchDTO;
use Domain\SearchBundle\Model\DataType\SearchResultsDTO;
use Symfony\Component\HttpFoundation\Request;
use Oxa\GeolocationBundle\Model\Geolocation\LocationValueObject;

class SearchDataUtil
{
    public static function buildResponceDTO() : SearchResultsDTO
    {
        return new SearchResultsDTO();
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
}
