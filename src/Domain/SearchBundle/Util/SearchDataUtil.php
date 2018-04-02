<?php

namespace Domain\SearchBundle\Util;

use Domain\SearchBundle\Model\DataType\EmergencySearchDTO;
use Domain\SearchBundle\Model\DataType\SearchDTO;
use Domain\SearchBundle\Model\DataType\SearchResultsDTO;
use Domain\SiteBundle\Utils\Helpers\SiteHelper;
use Symfony\Component\HttpFoundation\Request;
use Oxa\GeolocationBundle\Model\Geolocation\LocationValueObject;

class SearchDataUtil
{
    const ORDER_BY_RELEVANCE        = 'relevance';
    const ORDER_BY_DISTANCE         = 'distance';
    const DEFAULT_ORDER_BY_VALUE    = 'relevance';

    const DEFAULT_EMERGENCY_ORDER_BY_VALUE = self::EMERGENCY_ORDER_BY_ALPHABET;
    const EMERGENCY_ORDER_BY_DISTANCE = 'distance';
    const EMERGENCY_ORDER_BY_ALPHABET = 'alphabet';

    const EMERGENCY_FILTER_ALL_CHARACTER = 'all';
    const EMERGENCY_FILTER_LETTER        = 'letter';
    const EMERGENCY_FILTER_NUMBER        = 'number';
    const EMERGENCY_FILTER_OTHER         = 'other_char';

    const DEFAULT_ELASTIC_SEARCH_WORD_SEPARATOR = ' ';

    /**
     * @param array $resutlSet
     * @param int   $totalCount
     * @param int   $page
     * @param int   $pageCount
     * @param array $categories
     * @param \Doctrine\Common\Collections\ArrayCollection $neighborhoods
     *
     * @return SearchResultsDTO
     */
    public static function buildResponceDTO(
        $resutlSet,
        int $totalCount,
        int $page,
        int $pageCount,
        $categories = array(),
        $neighborhoods = array()
    ) : SearchResultsDTO {
        return new SearchResultsDTO($resutlSet, $totalCount, $page, $pageCount, $categories, $neighborhoods);
    }

    /**
     * @param string $query
     * @param LocationValueObject $location
     * @param int   $page
     * @param int   $limit
     *
     * @return SearchDTO
     */
    public static function buildRequestDTO($query, LocationValueObject $location, int $page, int $limit) : SearchDTO
    {
        return new SearchDTO($query, $location, $page, $limit);
    }

    /**
     * @param int $page
     * @param int $limit
     * @param int $areaId
     * @param int $categoryId
     * @param string $orderBy
     *
     * @return EmergencySearchDTO
     */
    public static function buildEmergencyRequestDTO($page, $limit, $areaId, $categoryId, $orderBy) : EmergencySearchDTO
    {
        return new EmergencySearchDTO($page, $limit, $areaId, $categoryId, $orderBy);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public static function getQueryFromRequest(Request $request)
    {
        return $request->get('q', null);
    }

    /**
     * @param Request $request
     *
     * @return int
     */
    public static function getPageFromRequest(Request $request) : int
    {
        return $request->get('page', 1);
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public static function getCategoryFromRequest(Request $request)
    {
        return $request->get('category', null);
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public static function getCatalogLocalityFromRequest(Request $request)
    {
        return $request->get('catalogLocality', null);
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public static function getNeighborhoodFromRequest(Request $request)
    {
        return $request->get('neighborhood', null);
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public static function getOrderByFromRequest(Request $request)
    {
        return $request->get('order', self::ORDER_BY_RELEVANCE);
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public static function getEmergencyCatalogOrderByFromRequest(Request $request)
    {
        return $request->get('order', self::DEFAULT_EMERGENCY_ORDER_BY_VALUE);
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public static function getEmergencyCatalogLatitudeFromRequest(Request $request)
    {
        return $request->get('lat', null);
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public static function getEmergencyCatalogLongitudeFromRequest(Request $request)
    {
        return $request->get('lng', null);
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public static function getEmergencyCatalogCharFilterFromRequest(Request $request)
    {
        $filter = $request->get('charFilter', null);

        if ($filter and !in_array($filter, self::getAllowedCharFilters())) {
            $filter = SiteHelper::getFirstSymbolFilter($filter);
        }

        return $filter;
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public static function getEmergencyServiceFiltersFromRequest(Request $request)
    {
        $filter = $request->get('serviceFilter', []);

        return $filter;
    }

    /**
     * @return array
     */
    public static function getAllowedCharFilters()
    {
        return [
            self::EMERGENCY_FILTER_ALL_CHARACTER,
            self::EMERGENCY_FILTER_NUMBER,
            self::EMERGENCY_FILTER_OTHER,
        ];
    }

    /**
     * @param $query string
     *
     * @return string
     */
    public static function sanitizeElasticSearchQueryString($query)
    {
        $string = str_replace(
            array_keys(self::getElasticSearchReservedChars()),
            self::DEFAULT_ELASTIC_SEARCH_WORD_SEPARATOR,
            mb_strtolower($query)
        );

        $string = preg_replace('/\s+/', self::DEFAULT_ELASTIC_SEARCH_WORD_SEPARATOR, $string);

        return $string;
    }

    /**
     * @return array
     */
    public static function getElasticSearchReservedChars()
    {
        //http://npm.taobao.org/package/elasticsearch-sanitize
        //http://lucene.apache.org/core/2_9_4/queryparsersyntax.html#Escaping
        //characters to escape: + - = && || > < ! ( ) { } [ ] ^ " ~ * ? : \ / AND OR NOT space

        $reservedChars = [
            '+'     => '\+',
            '-'     => '\-',
            '='     => '\=',
            '&&'    => '\&\&',
            '||'    => '\|\|',
            '>'     => '\>',
            '<'     => '\<',
            '!'     => '\!',
            '('     => '\(',
            ')'     => '\)',
            '{'     => '\{',
            '}'     => '\}',
            '['     => '\[',
            ']'     => '\]',
            '^'     => '\^',
            '"'     => '\"',
            '~'     => '\~',
            '*'     => '\*',
            '?'     => '\?',
            ':'     => '\:',
            '\\'    => '\\\\',
            '/'     => '\/',
            'AND'   => '\A\N\D',
            'OR'    => '\O\R',
            'NOT'   =>'\N\O\T',
        ];

        return $reservedChars;
    }
}
