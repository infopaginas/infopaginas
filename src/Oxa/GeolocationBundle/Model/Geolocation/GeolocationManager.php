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
        $geo = $request->get('geo', null);

        $lat = null;
        $lat = null;

        // todo - check is custom not from geolocation
        if ($geo) {
            if (1) {
                // get locality by name and locale

                $locality = $this->localityManager->getLocalityByNameAndLocale($geo, 'en');

                if ($locality) {
                    $lat = $locality->getLatitude();
                    $lng = $locality->getLongitude();
                }
            } else {
                $lat = $request->cookies->get('lat');
                $lng = $request->cookies->get('lng');
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
