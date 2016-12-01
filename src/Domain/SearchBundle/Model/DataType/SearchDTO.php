<?php

namespace Domain\SearchBundle\Model\DataType;

use Oxa\ManagerArchitectureBundle\Model\DataType\AbstractDTO;
use Doctrine\Common\Collections\ArrayCollection;
use Oxa\GeolocationBundle\Model\Geolocation\LocationValueObject;

class SearchDTO extends AbstractDTO
{
    public $query;
    public $locationValue;
    public $page;
    public $limit;

    protected $category;
    protected $subcategory;
    protected $catalogLocality;
    protected $neighborhood;

    protected $orderBy;

    public function __construct(string $query, LocationValueObject $locationValue, int $page, int $limit)
    {
        $this->query            = $query;
        $this->locationValue    = $locationValue;
        $this->page             = $page;
        $this->limit            = $limit;

        $this->category         = null;
        $this->subcategory      = null;
        $this->neighborhood     = null;

        $this->orderBy          = null;
    }

    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setSubcategory($subcategory)
    {
        $this->subcategory = $subcategory;

        return $this;
    }

    public function getSubcategory()
    {
        return $this->subcategory;
    }

    public function setCatalogLocality($subcategory)
    {
        $this->catalogLocality = $subcategory;

        return $this;
    }

    public function getCatalogLocality()
    {
        return $this->catalogLocality;
    }

    public function setNeighborhood($neighborhood)
    {
        $this->neighborhood = $neighborhood;

        return $this;
    }

    public function getNeighborhood()
    {
        return $this->neighborhood;
    }

    public function setOrderBy($order)
    {
        $this->orderBy = $order;
    }

    public function getOrderBy()
    {
        return $this->orderBy;
    }
}
