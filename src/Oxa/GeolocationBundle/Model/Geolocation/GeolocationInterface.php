<?php

namespace Oxa\GeolocationBundle\Model\Geolocation;

interface GeolocationInterface
{
    public function getLatitude();
    public function getLongitude();
    public function setLatitude($latitude);
    public function setLongitude($longitude);
}
