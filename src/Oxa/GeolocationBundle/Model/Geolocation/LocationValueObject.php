<?php

namespace Oxa\GeolocationBundle\Model\Geolocation;

use Oxa\ManagerArchitectureBundle\Model\DataType\AbstractValueObject;

class LocationValueObject extends AbstractValueObject
{
    public $name    = '';
    public $lat     = null;
    public $lng     = null;

    public $locality        = null;
    public $ignoreLocality  = false;

    public $userGeo = null;
    public $userLat = null;
    public $userLng = null;

    public $searchBoxTopLeftLat     = null;
    public $searchBoxTopLeftLng     = null;
    public $searchBoxBottomRightLat = null;
    public $searchBoxBottomRightLng = null;

    public $searchCenterLat = null;
    public $searchCenterLng = null;

    public function __construct($geoData = [])
    {
        if (!(empty($geoData['geo']))) {
            $this->name = $geoData['geo'];
        }

        if (!(empty($geoData['lat']))) {
            $this->lat = $geoData['lat'];
        }

        if (!(empty($geoData['lng']))) {
            $this->lng = $geoData['lng'];
        }

        if (!(empty($geoData['locality']))) {
            $this->locality = $geoData['locality'];
        }

        if (!(empty($geoData['ignoreLocality']))) {
            $this->ignoreLocality = $geoData['ignoreLocality'];
        }

        if (!(empty($geoData['userGeo']))) {
            $this->userGeo = $geoData['userGeo'];
        }

        if (!(empty($geoData['userLat']))) {
            $this->userLat = $geoData['userLat'];
        }

        if (!(empty($geoData['userLng']))) {
            $this->userLng = $geoData['userLng'];
        }

        if (!(empty($geoData['searchBoxTopLeftLat']))) {
            $this->searchBoxTopLeftLat = (float)$geoData['searchBoxTopLeftLat'];
        }

        if (!(empty($geoData['searchBoxTopLeftLng']))) {
            $this->searchBoxTopLeftLng = (float)$geoData['searchBoxTopLeftLng'];
        }

        if (!(empty($geoData['searchBoxBottomRightLat']))) {
            $this->searchBoxBottomRightLat = (float)$geoData['searchBoxBottomRightLat'];
        }

        if (!(empty($geoData['searchBoxBottomRightLng']))) {
            $this->searchBoxBottomRightLng = (float)$geoData['searchBoxBottomRightLng'];
        }

        if (!(empty($geoData['searchCenterLat']))) {
            $this->searchCenterLat = (float)$geoData['searchCenterLat'];
        }

        if (!(empty($geoData['searchCenterLng']))) {
            $this->searchCenterLng = (float)$geoData['searchCenterLng'];
        }
    }
}
