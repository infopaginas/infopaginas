<?php

namespace Domain\EmergencyBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Oxa\Sonata\AdminBundle\Model\ChangeStateInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\ChangeStateTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EmergencyService
 *
 * @ORM\Table(name="emergency_service")
 * @ORM\Entity(repositoryClass="Domain\EmergencyBundle\Repository\EmergencyServiceRepository")
 */
class EmergencyService implements ChangeStateInterface
{
    use ChangeStateTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string - service name
     *
     * @ORM\Column(name="name", type="string", length=100)
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @ORM\ManyToMany(
     *     targetEntity="Domain\EmergencyBundle\Entity\EmergencyBusiness",
     *     mappedBy="services",
     *     cascade={"persist"}
     *     )
     */
    protected $businesses;

    /**
     * @ORM\ManyToMany(
     *     targetEntity="Domain\EmergencyBundle\Entity\EmergencyDraftBusiness",
     *     mappedBy="services",
     *     cascade={"persist"}
     *     )
     */
    protected $draftBusinesses;

    /**
     * @var integer - service sorting position
     *
     * @ORM\Column(name="position", type="integer", nullable=true)
     */
    protected $position;

    /**
     * @var bool
     *
     * @ORM\Column(name="use_as_filter", type="boolean", options={"default" : 0})
     */
    protected $useAsFilter;

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
     * Constructor
     */
    public function __construct()
    {
        $this->businesses      = new ArrayCollection();
        $this->draftBusinesses = new ArrayCollection();

        $this->useAsFilter = false;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName() ? : '';
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return EmergencyService
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add businessProfile
     *
     * @param EmergencyBusiness $business
     *
     * @return EmergencyService
     */
    public function addBusiness(EmergencyBusiness $business)
    {
        $this->businesses->add($business);
        $business->addService($this);

        return $this;
    }

    /**
     * Remove $business
     *
     * @param EmergencyBusiness $business
     */
    public function removeBusiness(EmergencyBusiness $business)
    {
        $this->businesses->removeElement($business);
        $business->removeService($this);
    }

    /**
     * Get emergencyBusinesses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBusinesses()
    {
        return $this->businesses;
    }

    /**
     * Add draft business
     *
     * @param EmergencyDraftBusiness $business
     *
     * @return EmergencyService
     */
    public function addDraftBusiness(EmergencyDraftBusiness $business)
    {
        $this->draftBusinesses->add($business);
        $business->addService($this);

        return $this;
    }

    /**
     * Remove draft business
     *
     * @param EmergencyDraftBusiness $business
     */
    public function removeDraftBusiness(EmergencyDraftBusiness $business)
    {
        $this->draftBusinesses->removeElement($business);
        $business->removeService($this);
    }

    /**
     * Get emergencyBusinesses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDraftBusinesses()
    {
        return $this->businesses;
    }

    /**
     * Set position
     *
     * @param int $position
     *
     * @return EmergencyService
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param boolean $useAsFilter
     *
     * @return EmergencyService
     */
    public function setUseAsFilter($useAsFilter)
    {
        $this->useAsFilter = $useAsFilter;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getUseAsFilter()
    {
        return $this->useAsFilter;
    }
}
