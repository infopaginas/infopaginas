<?php

namespace Oxa\GeolocationBundle\Model\Geolocation;

use Oxa\ManagerArchitectureBundle;
use Symfony\Component\HttpFoundation\Request;
use Oxa\ManagerArchitectureBundle\Model\Manager\Manager;

use Doctrine\ORM\EntityManager;
use Oxa\ConfigBundle\Service\Config;
use Oxa\ConfigBundle\Model\ConfigInterface;

class GeolocationManager extends Manager
{
    protected $confingService;

    public function __construct(EntityManager $em, Config $confingService)
    {
        parent::__construct($em);

        $this->confingService = $confingService;
    }

    public function buildLocationValue(string $name, $lat = null, $lng = null)
    {
        return new LocationValueObject($name, $lat, $lng);
    }

    public function buildLocationValueFromRequest(Request $request)
    {
        $lat    = $request->cookies->get('lat', null);
        $lng    = $request->cookies->get('lng', null);

        $name   = $request->get('geo', null);

        if (!$name) {
            $name = $this->confingService->getSetting(ConfigInterface::DEFAULT_SEARCH_CITY)->getValue();
        }

        return $this->buildLocationValue($name, $lat, $lng);
    }
}
