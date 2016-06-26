<?php

namespace Oxa\GeolocationBundle\Model\Geolocation;

use Oxa\ManagerArchitectureBundle\Model\ValueObject;

class LocationValueObject extends ValueObject
{
    public $name;
    public $lat;
    public $lng;

    public function __construct(string $name, string $lat, string $lng)
    {
        $this->name = $name;
        $this->lat  = $lat;
        $this->lng  = $lng;
    }
}
