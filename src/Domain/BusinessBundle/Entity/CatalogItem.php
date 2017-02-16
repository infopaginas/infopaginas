<?php

namespace Domain\BusinessBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CatalogItem
 *
 * @ORM\Table(name="catalog_item")
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\CatalogItemRepository")
 */
class CatalogItem
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
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\Locality",
     *     inversedBy="catalogItems",
     *     )
     * @ORM\JoinColumn(name="locality_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    protected $locality;

    /**
     * @var $locality
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\Category",
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
     * @param Locality $locality
     *
     * @return CatalogItem
     */
    public function setLocality($locality)
    {
        $this->locality = $locality;

        return $this;
    }

    /**
     * @return Locality
     */
    public function getLocality()
    {
        return $this->locality;
    }

    /**
     * @param Category $category
     *
     * @return CatalogItem
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param boolean $hasContent
     *
     * @return CatalogItem
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
}
