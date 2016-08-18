<?php

namespace Domain\SearchBundle\Model\DataType;

use Oxa\ManagerArchitectureBundle\Model\DataType\AbstractDTO;

class DCDataDTO extends AbstractDTO
{
    public $searchKeywords;
    public $locationName;
    public $categories;

    public function __construct(array $searchKeywords = array(), string $locationName = '', $categories = array())
    {
        $this->searchKeywords  = $searchKeywords;
        $this->locationName    = $locationName;
        $this->categories      = $categories;

    }
}
