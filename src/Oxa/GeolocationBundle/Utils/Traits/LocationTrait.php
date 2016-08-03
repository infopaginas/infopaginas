<?php

namespace Oxa\GeolocationBundle\Utils\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class LocationTrait
 * @package Domain\BusinessBundle\Util\Traits
 */
trait LocationTrait
{
    /**
     * @var string
     *
     * @ORM\Column(name="latitude", type="float", nullable=true)
     */
    protected $latitude;

    /**
     * @var string
     *
     * @ORM\Column(name="longitude", type="float", nullable=true)
     */
    protected $longitude;

    /**
     * Set latitude
     *
     * @param string $latitude
     *
     * @return BusinessProfile
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return string
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param string $longitude
     *
     * @return BusinessProfile
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return string
     */
    public function getLongitude()
    {
        return $this->longitude;
    }
}
