<?php

namespace Domain\EmergencyBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Area
 *
 * @ORM\Table(name="emergency_area")
 * @ORM\Entity(repositoryClass="Domain\EmergencyBundle\Repository\EmergencyAreaRepository")
 */
class EmergencyArea
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string - Area name
     *
     * @ORM\Column(name="name", type="string", length=100)
     * @Assert\NotBlank()
     * @Assert\Length(max="100")
     */
    protected $name;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="Domain\EmergencyBundle\Entity\EmergencyBusiness",
     *      mappedBy="area",
     *      cascade={"persist"}
     * )
     */
    protected $businesses;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="Domain\EmergencyBundle\Entity\EmergencyDraftBusiness",
     *      mappedBy="area",
     *      cascade={"persist"}
     * )
     */
    protected $draftBusinesses;

    /**
     * @var string - Used to create human like url
     *
     * @Gedmo\Slug(fields={"name"}, updatable=false)
     * @ORM\Column(name="slug", type="string", length=255)
     */
    protected $slug;

    /**
     * @var integer - service sorting position
     *
     * @ORM\Column(name="position", type="integer", nullable=true)
     */
    protected $position;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="Domain\EmergencyBundle\Entity\EmergencyCatalogItem",
     *      mappedBy="area",
     * )
     */
    protected $catalogItems;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->businesses      = new ArrayCollection();
        $this->draftBusinesses = new ArrayCollection();
        $this->catalogItems    = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName() ?: '';
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
     * Set name
     *
     * @param string $name
     *
     * @return EmergencyArea
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
     * Add business
     *
     * @param EmergencyBusiness $business
     *
     * @return EmergencyArea
     */
    public function addBusiness(EmergencyBusiness $business)
    {
        $this->businesses->add($business);

        return $this;
    }

    /**
     * Remove business
     *
     * @param EmergencyBusiness $business
     */
    public function removeBusiness(EmergencyBusiness $business)
    {
        $this->businesses->removeElement($business);
    }

    /**
     * Get businesses
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
     * @return EmergencyArea
     */
    public function addDraftBusiness(EmergencyDraftBusiness $business)
    {
        $this->draftBusinesses->add($business);

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
    }

    /**
     * Get draft businesses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDraftBusinesses()
    {
        return $this->draftBusinesses;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return EmergencyCategory
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set position
     *
     * @param int $position
     *
     * @return EmergencyArea
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
     * @return ArrayCollection
     */
    public function getCatalogItems()
    {
        return $this->catalogItems;
    }

    /**
     * Add catalogItem
     *
     * @param EmergencyCatalogItem $catalogItem
     *
     * @return EmergencyArea
     */
    public function addCatalogItem(EmergencyCatalogItem $catalogItem)
    {
        $this->catalogItems->add($catalogItem);

        return $this;
    }

    /**
     * @param EmergencyCatalogItem $catalogItem
     *
     * @return EmergencyArea
     */
    public function removeCatalogItems(EmergencyCatalogItem $catalogItem)
    {
        $this->catalogItems->removeElement($catalogItem);

        return $this;
    }
}
