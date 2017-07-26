<?php

namespace Domain\SearchBundle\Model\DataType;

use Domain\SearchBundle\Util\SearchDataUtil;
use Oxa\ManagerArchitectureBundle\Model\DataType\AbstractDTO;
use Oxa\GeolocationBundle\Model\Geolocation\LocationValueObject;

class SearchDTO extends AbstractDTO
{
    /**
     * @var string $query
     */
    public $query;

    /**
     * @var LocationValueObject $locationValue
     */
    public $locationValue;

    /**
     * @var int $page
     */
    public $page;

    /**
     * @var int $limit
     */
    public $limit;

    public $adsAllowed;
    public $adsMaxPages;    // adsMaxPages = 0, means all pages
    public $adsPerPage;

    protected $category;
    protected $catalogLocality;
    protected $neighborhood;

    protected $orderBy;

    /**
     * @var bool $isRandomized
     */
    protected $isRandomized;

    /**
     * @param string $query
     * @param LocationValueObject   $locationValue
     * @param int   $page
     * @param int   $limit
     */
    public function __construct(string $query, LocationValueObject $locationValue, int $page, int $limit)
    {
        $this->query            = $query;
        $this->locationValue    = $locationValue;
        $this->page             = $page;
        $this->limit            = $limit;

        $this->category         = null;
        $this->neighborhood     = null;

        $this->orderBy          = null;

        $this->adsAllowed       = false;
        $this->adsMaxPages      = 0;
        $this->adsPerPage       = 0;
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

    /**
     * @return array
     */
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

    /**
     * @return bool
     */
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

    /**
     * @param bool $isRandomized
     */
    public function setIsRandomized($isRandomized)
    {
        $this->isRandomized = $isRandomized;
    }

    /**
     * @return bool
     */
    public function getIsRandomized()
    {
        return $this->isRandomized;
    }

    /**
     * @return bool
     */
    public function randomizeAllowed()
    {
        $randomizeAllowed = false;

        if ($this->isRandomized and SearchDataUtil::ORDER_BY_RELEVANCE == $this->getOrderBy()) {
            $randomizeAllowed = true;
        }

        return $randomizeAllowed;
    }

    /**
     * @return bool
     */
    public function checkAdsAllowed()
    {
        if ($this->adsAllowed and $this->adsPerPage > 0 and
            ($this->adsMaxPages === 0 or $this->adsMaxPages >= $this->page)
        ) {
            return true;
        }

        return false;
    }
}
