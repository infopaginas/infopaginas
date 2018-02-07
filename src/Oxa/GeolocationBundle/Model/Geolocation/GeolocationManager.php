<?php

namespace Oxa\GeolocationBundle\Model\Geolocation;

use Domain\BusinessBundle\Entity\Locality;
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

    /**
     * @param EntityManager $em
     * @param Config $confingService
     * @param LocalityManager $localityManager
     */
    public function __construct(EntityManager $em, Config $confingService, LocalityManager $localityManager)
    {
        parent::__construct($em);

        $this->confingService = $confingService;
        $this->localityManager = $localityManager;
    }

    /**
     * @param array $geoData
     *
     * @return LocationValueObject
     */
    public function buildLocationValue($geoData)
    {
        return new LocationValueObject($geoData);
    }

    /**
     * @param Request $request
     * @param bool $useUserGeo
     *
     * @return LocationValueObject|null
     */
    public function buildLocationValueFromRequest(Request $request, $useUserGeo = true)
    {
        $geo    = $request->get('geo', null);

        $lat        = null;
        $lng        = null;
        $locality   = null;

        $userLat    = null;
        $userLng    = null;
        $userGeo    = null;

        $searchBoxTopLeftLat = $request->get('tllt', null);
        $searchBoxTopLeftLng = $request->get('tllg', null);
        $searchBoxBottomRightLat = $request->get('brlt', null);
        $searchBoxBottomRightLng = $request->get('brlg', null);
        $searchCenterLat = $request->get('clt', null);
        $searchCenterLng = $request->get('clg', null);

        if ($useUserGeo) {
            $userLat    = $request->get('lat', null);
            $userLng    = $request->get('lng', null);
            $userGeo    = $request->get('geoLoc', null);
        }

        $ignoreLocality = false;

        if ($geo and $geo != Locality::ALL_LOCALITY) {
            // get locality by name and locale
            $locality = $this->localityManager->getLocalityByName($geo);
        } else {
            // empty search - show default
            $locality = $this->localityManager->getLocalityByName(
                $this->confingService->getValue(ConfigInterface::DEFAULT_SEARCH_CITY)
            );

            $request->query->set('geo', Locality::ALL_LOCALITY);

            $ignoreLocality = true;
        }

        if ($locality) {
            $lat = $locality->getLatitude();
            $lng = $locality->getLongitude();
        }

        if ($lat and $lng) {
            $geoData = [
                'geo'                       => $geo,
                'lat'                       => $lat,
                'lng'                       => $lng,
                'locality'                  => $locality,
                'ignoreLocality'            => $ignoreLocality,
                'userGeo'                   => $userGeo,
                'userLat'                   => $userLat,
                'userLng'                   => $userLng,
                'searchBoxTopLeftLat'       => $searchBoxTopLeftLat,
                'searchBoxTopLeftLng'       => $searchBoxTopLeftLng,
                'searchBoxBottomRightLat'   => $searchBoxBottomRightLat,
                'searchBoxBottomRightLng'   => $searchBoxBottomRightLng,
                'searchCenterLat'           => $searchCenterLat,
                'searchCenterLng'           => $searchCenterLng,
            ];

            $return = $this->buildLocationValue($geoData);
        } else {
            $return = null;
        }

        return $return;
    }

    /**
     * @param array $params
     *
     * @return LocationValueObject
     */
    public function buildLocationValueFromApi($params)
    {
        $geoData = [
            'lat' => (float)$params['lat'],
            'lng' => (float)$params['lng'],
        ];

        return $this->buildLocationValue($geoData);
    }

    /**
     * @param Locality $locality
     * @param null|float $latitude
     * @param null|float $longitude
     * @return LocationValueObject|null
     */
    public function buildCatalogLocationValue($locality, $latitude = null, $longitude = null)
    {
        return $this->buildElasticLocationValue($locality, $latitude, $longitude);
    }

    /**
     * @param Locality $locality
     * @param null|float $latitude
     * @param null|float $longitude
     * @return LocationValueObject|null
     */
    public function buildElasticLocationValue($locality, $latitude = null, $longitude = null)
    {
        $locationValueObject = null;

        if ($locality) {
            $lat = $latitude ?: $locality->getLatitude();
            $lng = $longitude ?: $locality->getLongitude();

            if ($lat and $lng) {
                $geoData = [
                    'geo' => $locality->getName(),
                    'lat' => $lat,
                    'lng' => $lng,
                    'locality' => $locality,
                ];

                $locationValueObject = $this->buildLocationValue($geoData);
            }
        }

        return $locationValueObject;
    }
}
