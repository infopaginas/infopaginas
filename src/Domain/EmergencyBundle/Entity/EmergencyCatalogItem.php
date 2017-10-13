<?php

namespace Domain\EmergencyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CatalogItem
 *
 * @ORM\Table(name="emergency_catalog_item")
 * @ORM\Entity(repositoryClass="Domain\EmergencyBundle\Repository\EmergencyCatalogItemRepository")
 */
class EmergencyCatalogItem
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
     * @var $locality
     * @ORM\ManyToOne(targetEntity="Domain\EmergencyBundle\Entity\EmergencyArea",
     *     inversedBy="catalogItems",
     *     )
     * @ORM\JoinColumn(name="area_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    protected $area;

    /**
     * @var $locality
     * @ORM\ManyToOne(targetEntity="Domain\EmergencyBundle\Entity\EmergencyCategory",
     *     inversedBy="catalogItems",
     *     )
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    protected $category;

    /**
     * @var boolean
     *
     * @ORM\Column(name="has_content", type="boolean", options={"default" : false})
     */
    protected $hasContent;

    /**
     * @var \DateTime
     * @ORM\Column(name="content_updated_at", type="datetime", nullable=true)
     */
    protected $contentUpdatedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="filters", type="text", nullable=true)
     */
    protected $filters;

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
        $this->hasContent = false;
    }

    /**
     * @param EmergencyArea $area
     *
     * @return EmergencyCatalogItem
     */
    public function setArea($area)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * @return EmergencyArea
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * @param EmergencyCategory $category
     *
     * @return EmergencyCatalogItem
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return EmergencyCategory
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param boolean $hasContent
     *
     * @return EmergencyCatalogItem
     */
    public function setHasContent($hasContent)
    {
        $this->hasContent = $hasContent;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getHasContent()
    {
        return $this->hasContent;
    }

    /**
     * @param \DateTime $contentUpdatedAt
     *
     * @return EmergencyCatalogItem
     */
    public function setContentUpdatedAt($contentUpdatedAt)
    {
        $this->contentUpdatedAt = $contentUpdatedAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getContentUpdatedAt()
    {
        return $this->contentUpdatedAt;
    }

    /**
     * @param string $filters
     *
     * @return EmergencyCatalogItem
     */
    public function setFilters($filters)
    {
        $this->filters = $filters;

        return $this;
    }

    /**
     * @return string
     */
    public function getFilters()
    {
        return $this->filters;
    }
}
