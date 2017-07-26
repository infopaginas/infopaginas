<?php

namespace Domain\BusinessBundle\Model\DataType;

use Oxa\ManagerArchitectureBundle\Model\DataType\AbstractDTO;

/**
 * Class ReviewsResultsDTO
 * Use it to keep all required info about reviews DB request in single place
 *
 * @package Domain\BusinessBundle\Model\DataType
 */
class ReviewsListQueryParamsDTO extends AbstractDTO
{
    /** @var int Reviews per page count */
    public $limit;

    /** @var int current page */
    public $page;

    /**
     * ReviewsListQueryParamsDTO constructor.
     *
     * @param int $limit
     * @param int $page
     */
    public function __construct(int $limit, int $page)
    {
        $this->limit = $limit;
        $this->page  = $page;
    }
}
