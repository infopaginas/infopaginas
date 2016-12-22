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

    public function buildLocationValue(string $name, $lat = null, $lng = null, $locality = null)
    {
        return new LocationValueObject($name, $lat, $lng, $locality);
    }

    public function buildLocationValueFromRequest(Request $request)
    {
        $geo    = $request->get('geo', null);
        $geoLoc = $request->get('geoLoc', null);

        $lat        = null;
        $lng        = null;
        $locality   = null;

        if ($geo) {
            // get locality by name and locale

            $locality = $this->localityManager->getLocalityByNameAndLocale($geo, $request->getLocale());

            // check is custom geo request not from geolocation - use coordinates
            if ($geoLoc == $geo) {
                $lat = $request->get('lat', null);
                $lng = $request->get('lng', null);
            }
        } else {
            // empty search - show default

            $locality = $this->localityManager->getLocalityByNameAndLocale(
                $this->confingService->getValue(ConfigInterface::DEFAULT_SEARCH_CITY),
                $request->getLocale()
            );

            $geo = $locality->getName();
            $request->request->set('geo', $geo);
        }

        if ($locality and !$lat) {
            $lat = $locality->getLatitude();
            $lng = $locality->getLongitude();
        }

        if ($lat and $lng) {
            $return = $this->buildLocationValue($geo, $lat, $lng, $locality);
        } else {
            $return = null;
        }

        return $return;
    }
}
