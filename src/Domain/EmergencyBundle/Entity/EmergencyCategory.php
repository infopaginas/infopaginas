<?php

namespace Domain\EmergencyBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EmergencyCategory
 *
 * @ORM\Table(name="emergency_category")
 * @ORM\Entity(repositoryClass="Domain\EmergencyBundle\Repository\EmergencyCategoryRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class EmergencyCategory
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
     * @var string - Category name
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(max="255")
     */
    protected $name;

    /**
     * @var string - Category search name
     *
     * @ORM\Column(name="search_name", type="string", length=255, options={"default": ""})
     */
    protected $searchName;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="Domain\EmergencyBundle\Entity\EmergencyBusiness",
     *      mappedBy="category",
     *      cascade={"persist"}
     * )
     */
    protected $businesses;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="Domain\EmergencyBundle\Entity\EmergencyDraftBusiness",
     *      mappedBy="category",
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
     * @ORM\Column(name="position", type="integer")
     * @Assert\NotBlank()
     */
    protected $position;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="Domain\EmergencyBundle\Entity\EmergencyCatalogItem",
     *      mappedBy="category",
     * )
     */
    protected $catalogItems;

    protected $changeState;

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
     * @return EmergencyCategory
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
     * @param string $searchName
     *
     * @return EmergencyCategory
     */
    public function setSearchName($searchName)
    {
        $this->searchName = $searchName;

        return $this;
    }

    /**
     * @return string
     */
    public function getSearchName()
    {
        return $this->searchName;
    }

    /**
     * Add business
     *
     * @param EmergencyBusiness $business
     *
     * @return EmergencyCategory
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
     * @return EmergencyCategory
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
     * @return EmergencyCategory
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
     * @return EmergencyCategory
     */
    public function addCatalogItem(EmergencyCatalogItem $catalogItem)
    {
        $this->catalogItems->add($catalogItem);

        return $this;
    }

    /**
     * @param EmergencyCatalogItem $catalogItem
     *
     * @return EmergencyCategory
     */
    public function removeCatalogItems(EmergencyCatalogItem $catalogItem)
    {
        $this->catalogItems->removeElement($catalogItem);

        return $this;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function updateSearchName()
    {
        $this->searchName = AdminHelper::convertAccentedString($this->name);
    }

    public function getChangeState()
    {
        return $this->changeState;
    }

    public function setChangeState(array $changeState) : self
    {
        $this->changeState = $changeState;

        return $this;
    }
}
