<?php

namespace Domain\SearchBundle\Model\DataType;

use Domain\SearchBundle\Util\SearchDataUtil;
use Oxa\ManagerArchitectureBundle\Model\DataType\AbstractDTO;
use Doctrine\Common\Collections\ArrayCollection;
use Oxa\GeolocationBundle\Model\Geolocation\LocationValueObject;

class SearchDTO extends AbstractDTO
{
    public $query;
    public $locationValue;
    public $page;
    public $limit;

    protected $category1;
    protected $category2;
    protected $category3;
    protected $catalogLocality;
    protected $neighborhood;

    protected $orderBy;
    protected $isRandomized;

    public function __construct(string $query, LocationValueObject $locationValue, int $page, int $limit)
    {
        $this->query            = $query;
        $this->locationValue    = $locationValue;
        $this->page             = $page;
        $this->limit            = $limit;

        $this->category1        = null;
        $this->category2        = null;
        $this->category3        = null;
        $this->neighborhood     = null;

        $this->orderBy          = null;
    }

    public function setCategory1($category)
    {
        $this->category1 = $category;

        return $this;
    }

    public function getCategory1()
    {
        return $this->category1;
    }

    public function setCategory2($category2)
    {
        $this->category2 = $category2;

        return $this;
    }

    public function getCategory2()
    {
        return $this->category2;
    }

    public function setCategory3($category3)
    {
        $this->category3 = $category3;

        return $this;
    }

    public function getCategory3()
    {
        return $this->category3;
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

    public function getCurrentCoordinates()
    {
        if ($this->locationValue->userLat and $this->locationValue->userLng) {
            // geo location on
            $currentLat = $this->locationValue->userLat;
            $currentLng = $this->locationValue->userLng;
        } else {
            // geo location off
            $currentLat = $this->locationValue->lat;
            $currentLng = $this->locationValue->lng;
        }

        return [
            'lat' => $currentLat,
            'lng' => $currentLng,
        ];
    }

    public function checkSearchInMap()
    {
        $location = $this->locationValue;

        if ($location->searchBoxTopLeftLat and $location->searchBoxTopLeftLng and
            $location->searchBoxBottomRightLat and $location->searchBoxBottomRightLng
        ) {
            return true;
        }

        return false;
    }

    public function setIsRandomized($isRandomized)
    {
        $this->isRandomized = $isRandomized;
    }

    public function randomizeAllowed()
    {
        $randomizeAllowed = false;

        if ($this->isRandomized and SearchDataUtil::ORDER_BY_RELEVANCE == $this->getOrderBy()) {
            $randomizeAllowed = true;
        }

        return $randomizeAllowed;
    }
}
