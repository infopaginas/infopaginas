<?php

namespace Oxa\GeolocationBundle\Utils;

class GeolocationUtils
{
    const EARTH_RADIUS_KM = 6371;

    public static function getEarthRadiusKm()
    {
        return self::EARTH_RADIUS_KM;
    }

    public static function getEarthDiameterKm()
    {
        return self::EARTH_RADIUS_KM * 2;
    }
}
