<?php

namespace Oxa\GeolocationBundle\Utils;

class GeolocationUtils
{
    protected static $ch;

    const EARTH_RADIUS_KM   = 6371;
    const MILE_TO_KILOMETER = 0.621371;

    const GEO_CODE_URL_BASE         = "https://maps.googleapis.com/maps/api/geocode/json?sensor=true&latlng=";
    const GEO_CODE_APP_CONTENT_TYPE = 'Content-Type: application/json';
    const GEO_CODE_APP_ACCEPT_TYPE  = 'Accept: application/json';

    public static function getEarthDiameterMiles()
    {
        return self::EARTH_RADIUS_KM * 2 * self::MILE_TO_KILOMETER;
    }

    protected static function initCurl()
    {
        self::$ch = curl_init();

        curl_setopt(self::$ch, CURLOPT_CONNECTTIMEOUT, 60); // 1 munute
        curl_setopt(self::$ch, CURLOPT_TIMEOUT, 600); // 10 minutes
        curl_setopt(self::$ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(self::$ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt(self::$ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt(self::$ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt(self::$ch, CURLOPT_HEADER, true);

        return true;
    }

    protected static function getContentTypes()
    {
        return array(
            self::GEO_CODE_APP_CONTENT_TYPE,
            self::GEO_CODE_APP_ACCEPT_TYPE
        );
    }

    public static function getCityByGeolocation($lat, $lng)
    {
        self::initCurl();

        curl_setopt(self::$ch, CURLOPT_URL, self::buildGeoCodeURL($lat, $lng));
        curl_setopt(
            self::$ch,
            CURLOPT_HTTPHEADER,
            self::getContentTypes()
        );

        $result = curl_exec(self::$ch);
        $info = curl_getinfo(self::$ch);

        if ($info['http_code'] == 200) {
            return json_decode(
                substr(
                    $result,
                    strpos(
                        $result,
                        '{'
                    )
                ),
                true
            )['results'];
        }

        return false;
    }

    protected static function buildGeoCodeURL($lat, $lng)
    {
        return self::GEO_CODE_URL_BASE . $lat . ',' . $lng;
    }

    public static function filterResults(array $results)
    {
        $cityName = null;

        array_filter($results, function ($item) use (&$cityName) {
            return  array_filter($item['address_components'], function ($components) use (&$cityName) {
                if (in_array('locality', $components['types'])) {
                    $cityName = $components['long_name'];
                }
            });
        });

        return $cityName;
    }

    public static function getDistanceForPoint($userLatitude, $userLongitude, $itemLatitude, $itemLongitude)
    {
        $distance = GeolocationUtils::getEarthDiameterMiles() * sin(
            sqrt(
                (1 - cos(($itemLatitude - $userLatitude) * PI()/180)) / 2
                +
                cos($userLatitude * PI()/180)
                *
                cos($itemLatitude * PI()/180)
                *
                (1 - cos(($itemLongitude - $userLongitude) * PI()/180)) / 2
            )
        );

        return $distance;
    }
}
