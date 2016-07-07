<?php

namespace Oxa\GeolocationBundle\Model\Geolocation;

use Oxa\ManagerArchitectureBundle\Model\DataType\AbstractValueObject;

class LocationValueObject extends AbstractValueObject
{
    public $name;
    public $lat;
    public $lng;

    public function __construct(string $name, string $lat, string $lng)
    {
        if (null === $name && null  === $lat && null === $lng) {
            throw new Exception("All params can not be NULL", 1);
        }

        $this->name = $name;
        $this->lat  = $lat;
        $this->lng  = $lng;
    }
}
