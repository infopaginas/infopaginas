<?php

namespace Domain\SearchBundle\Model\DataType;

use Oxa\ManagerArchitectureBundle\Model\DataType\AbstractDTO;
use Doctrine\Common\Collections\ArrayCollection;

class SearchResultsDTO extends AbstractDTO
{
    public $resultSet;     // array/ArrayCollection of results
    public $resultCount;   // total amount of results
    public $page;          // current page
    public $pageCount;     // total amount of pages
    public $categories;    // list of related categories
    public $neighborhoods; // ArrayCollection of neighborhoods

    public function __construct(
        array $resultSet,
        int $resultCount,
        int $page,
        int $pageCount,
        array $categories,
        $neighborhoods
    ) {
        $this->resultSet        = $resultSet;
        $this->resultCount      = $resultCount;
        $this->page             = $page;
        $this->pageCount        = $pageCount;
        $this->categories       = $categories;
        $this->neighborhoods    = $neighborhoods;
    }
}
