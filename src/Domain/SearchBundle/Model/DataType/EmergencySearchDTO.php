<?php

namespace Domain\SearchBundle\Model\DataType;

use Oxa\ManagerArchitectureBundle\Model\DataType\AbstractDTO;

class EmergencySearchDTO extends AbstractDTO
{
    /**
     * @var int
     */
    public $page;

    /**
     * @var int
     */
    public $limit;

    /**
     * @var int
     */
    public $areaId;

    /**
     * @var int
     */
    public $categoryId;

    /**
     * @var string
     */
    public $orderBy;

    /**
     * @var string
     */
    public $characterFilter;

    /**
     * @var float|null
     */
    public $lat = null;

    /**
     * @var float|null
     */
    public $lng = null;

    /**
     * @param int $page
     * @param int $limit
     * @param int $areaId
     * @param int $categoryId
     * @param string $orderBy
     */
    public function __construct($page, $limit, $areaId, $categoryId, $orderBy)
    {
        $this->page         = $page;
        $this->limit        = $limit;
        $this->areaId       = $areaId;
        $this->categoryId   = $categoryId;
        $this->orderBy      = $orderBy;
    }

    public function sortingByDistanceAvailable()
    {
        return $this->lat and $this->lng;
    }
}
