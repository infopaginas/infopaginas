<?php

namespace Domain\BusinessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

use Domain\BusinessBundle\Entity\Area;
use Oxa\GeolocationBundle\Utils\Traits\LocationTrait;
use Oxa\GeolocationBundle\Model\Geolocation\GeolocationInterface;

/**
 * Locality
 *
 * @ORM\Table(name="localities")
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\LocalityRepository")
 */
class Locality implements GeolocationInterface
{
    use LocationTrait;
 
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string - Locality name
     *
     * @ORM\Column(name="name", type="string", length=100)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\ManyToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\BusinessProfile",
     *     mappedBy="localities",
     *     cascade={"persist"}
     * )
     */
    private $businessProfile;

    /**
     * @var Domain\BusinessBundle\Entity\Area
     *
     * @ORM\ManyToOne(
     *      targetEntity="Domain\BusinessBundle\Entity\Area",
     *      inversedBy="locality"
     * )
     * @ORM\JoinColumn(name="area_id", referencedColumnName="id")
     */
    protected $area;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->businessProfile = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Locality
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBusinessProfile()
    {
        return $this->businessProfile;
    }

    /**
     * @param mixed $businessProfile
     * @return Locality
     */
    public function setBusinessProfile($businessProfile)
    {
        $this->businessProfile = $businessProfile;
        return $this;
    }

    /**
     * Add businessProfile
     *
     * @param \Domain\BusinessBundle\Entity\BusinessProfile $businessProfile
     *
     * @return Locality
     */
    public function addBusinessProfile(\Domain\BusinessBundle\Entity\BusinessProfile $businessProfile)
    {
        $this->businessProfile[] = $businessProfile;
    }

    /**
     * @param BusinessProfile $businessProfile
     * @return $this
     */
    public function removeBusinessProfile(\Domain\BusinessBundle\Entity\BusinessProfile $businessProfile)
    {
        $this->businessProfile->removeElement($businessProfile);

        return $this;
    }
    
    /**
     *  Get owning area for this locality
     *
     * @return Area
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     *  Set owning area for this locality
     *
     * @param Area $area
     * @return $this
     */
    public function setArea(Area $area)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName() ?: '';
    }
}
