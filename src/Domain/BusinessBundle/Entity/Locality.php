<?php

namespace Domain\BusinessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Locality
 *
 * @ORM\Table(name="localities")
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\LocalityRepository")
 */
class Locality
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string - Locality name
     *
     * @ORM\Column(name="name", type="string", length=100)
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
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}
