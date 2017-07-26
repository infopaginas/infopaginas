<?php

namespace Domain\BusinessBundle\Model\DataType;

use Oxa\ManagerArchitectureBundle\Model\DataType\AbstractDTO;

/**
 * Class ReviewsResultsDTO
 * Used to profile single object with all requred data for paginator on reviews page
 *
 * @package Domain\BusinessBundle\Model\DataType
 */
class ReviewsResultsDTO extends AbstractDTO
{
    /** @var array collection of results */
    public $resultSet;

    /** @var int total results count */
    public $resultCount;

    /** @var int current page number */
    public $page;

    /** @var int total pages count */
    public $pageCount;

    /**
     * ReviewsResultsDTO constructor.
     *
     * @param array $resultSet
     * @param int $resultCount
     * @param int $page
     * @param int $pageCount
     */
    public function __construct(
        array $resultSet,
        int $resultCount,
        int $page,
        int $pageCount
    ) {
        $this->resultSet    = $resultSet;
        $this->resultCount  = $resultCount;
        $this->page         = $page;
        $this->pageCount    = $pageCount;
    }
}
