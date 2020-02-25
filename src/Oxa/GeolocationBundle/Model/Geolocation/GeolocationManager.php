<?php

namespace Oxa\GeolocationBundle\Model\Geolocation;

use Domain\BusinessBundle\Entity\Locality;
use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\SearchBundle\Model\Manager\SearchManager;
use Domain\SiteBundle\Utils\Helpers\LocaleHelper;
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
    protected $localityManager;
    protected $bpManager;

    /**
     * @param EntityManager $em
     * @param Config $confingService
     * @param LocalityManager $localityManager
     * @param BusinessProfileManager $bpManager
     */
    public function __construct(
        EntityManager $em,
        Config $confingService,
        LocalityManager $localityManager,
        BusinessProfileManager $bpManager
    ) {
        parent::__construct($em);

        $this->confingService = $confingService;
        $this->localityManager = $localityManager;
        $this->bpManager = $bpManager;
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
        $locale = LocaleHelper::getLocale($request->getLocale());

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

        if ($geo && $geo != Locality::ALL_LOCALITY) {
            // get locality by name
            $localityData = $this->bpManager->searchLocalityAutoSuggestInElastic(
                SearchManager::getSafeSearchString($geo),
                $locale,
                1
            );
            $locality = !empty($localityData[0]['id']) ?
                $this->em->getRepository(Locality::class)->find($localityData[0]['id']) : null;
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
            $request->query->set('geo', $locality->getTranslation('name', $locale));
            $geo = $locality->getTranslation('name', $locale);
        }

        if ($lat && $lng) {
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
     * @return LocationValueObject|null
     */
    public function buildCatalogLocationValue($locality)
    {
        return $this->buildElasticLocationValue($locality);
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

            if ($lat && $lng) {
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
