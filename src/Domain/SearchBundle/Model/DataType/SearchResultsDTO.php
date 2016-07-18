<?php

namespace Domain\SearchBundle\Model\DataType;

use Oxa\ManagerArchitectureBundle\Model\DataType\AbstractDTO;
use Doctrine\Common\Collections\ArrayCollection;

class SearchResultsDTO extends AbstractDTO
{
    protected $resultSet;
    protected $resultCount;
    protected $page;
    protected $pageCount;

    public function __construct(ArrayCollection $resultSet, int $resultCount, int $page, int $pageCount)
    {
        $this->resultSet        = $resultSet;
        $this->resultCount      = $resultCount;
        $this->page             = $page;
        $this->pageCount        = $pageCount;
    }
}
