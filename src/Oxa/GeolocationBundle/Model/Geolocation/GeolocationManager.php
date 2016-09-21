<?php

namespace Oxa\GeolocationBundle\Model\Geolocation;

use Oxa\ManagerArchitectureBundle;
use Symfony\Component\HttpFoundation\Request;
use Oxa\ManagerArchitectureBundle\Model\Manager\Manager;

use Doctrine\ORM\EntityManager;
use Oxa\ConfigBundle\Service\Config;
use Oxa\ConfigBundle\Model\ConfigInterface;
use Oxa\GeolocationBundle\Utils\GeolocationUtils;
use Domain\BusinessBundle\Manager\LocalityManager;

class GeolocationManager extends Manager
{
    protected $confingService;

    public function __construct(EntityManager $em, Config $confingService, LocalityManager $localityManager)
    {
        parent::__construct($em);

        $this->confingService = $confingService;
        $this->localityManager = $localityManager;
    }

    public function buildLocationValue(string $name, $lat = null, $lng = null, $zip = null)
    {
        return new LocationValueObject($name, $lat, $lng, $zip);
    }

    public function buildLocationValueFromRequest(Request $request)
    {
        $geo    = $request->get('geo', null);
        $geoLoc = $request->get('geoLoc', null);

        $lat = null;
        $lat = null;

        if ($geo) {
            // check is custom geo request not from geolocation

            if ($geoLoc == $geo) {
                $lat = $request->get('lat', null);
                $lng = $request->get('lng', null);
            } else {
                // get locality by name and locale

                $locality = $this->localityManager->getLocalityByNameAndLocale($geo, $request->getLocale());

                if ($locality) {
                    $lat = $locality->getLatitude();
                    $lng = $locality->getLongitude();
                }
            }
        } else {
            // empty search - show default
            $geo = $this->confingService->getValue(ConfigInterface::DEFAULT_SEARCH_CITY);
            $lat = $this->confingService->getValue(ConfigInterface::DEFAULT_MAP_COORDINATE_LATITUDE);
            $lng = $this->confingService->getValue(ConfigInterface::DEFAULT_MAP_COORDINATE_LONGITUDE);
        }

        if ($lat and $lng) {
            //todo - remove zip
            $return = $this->buildLocationValue($geo, $lat, $lng, null);
        } else {
            $return = null;
        }

        return $return;
    }
}
