<?php

namespace Oxa\GeolocationBundle\Model\Geolocation;

use Oxa\ManagerArchitectureBundle;
use Symfony\Component\HttpFoundation\Request;
use Oxa\ManagerArchitectureBundle\Model\Manager\Manager;

class GeolocationManager extends Manager
{
    public function buildLocationValue(string $name, $lat = null, $lng = null)
    {
        return new LocationValueObject($name, $lat, $lng);
    }

    public function buildLocationValueFromRequest(Request $request)
    {
        $lat    = $request->cookies->get('lat', null);
        $lng    = $request->cookies->get('lng', null);

        $name   = $request->get('geo', null);

        return $this->buildLocationValue($name, $lat, $lng);
    }
}
