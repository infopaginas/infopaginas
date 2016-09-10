<?php

namespace Domain\ReportBundle\Model\DataType;

/**
 * Class ReportDatesRangeVO
 * Value object which incapsulate date range data inside itself
 *
 * More detailed: see "Money" software design pattern
 *
 * @link http://martinfowler.com/bliki/ValueObject.html
 * @package Domain\ReportBundle\Model\DataType
 */
class ReportDatesRangeVO
{
    /** @var \DateTime $start Report since date */
    protected $start;

    /** @var \DateTime $end Report till date */
    protected $end;

    /**
     * ReportDatesRangeVO constructor.
     *
     * @access public
     * @param \DateTime $start
     * @param \DateTime $end
     */
    public function __construct(\DateTime $start, \DateTime $end)
    {
        $this->start = $start;
        $this->end   = $end;
    }

    /**
     * Report start date getter
     *
     * @access public
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->start;
    }

    /**
     * Report end date getter
     *
     * @access public
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->end;
    }
}
