<?php

namespace Domain\SearchBundle\Model\DataType;

use Oxa\ManagerArchitectureBundle\Model\DataType\AbstractDTO;
use Doctrine\Common\Collections\ArrayCollection;

class SearchResultsDTO extends AbstractDTO
{
    /**
     * @var array|ArrayCollection $resultSet
     */
    public $resultSet;

    /**
     * total amount of results
     *
     * @var int $resultCount
     */
    public $resultCount;

    /**
     * current page
     *
     * @var int $page
     */
    public $page;

    /**
     * total amount of pages
     *
     * @var int $pageCount
     */
    public $pageCount;

    /**
     * list of related categories
     *
     * @var array $categories
     */
    public $categories;

    /**
     * @var ArrayCollection $neighborhoods
     */
    public $neighborhoods;

    /**
     * @param array|ArrayCollection $resultSet
     * @param int $resultCount
     * @param int $page
     * @param int $pageCount
     * @param array $categories
     * @param ArrayCollection $neighborhoods
     */
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
