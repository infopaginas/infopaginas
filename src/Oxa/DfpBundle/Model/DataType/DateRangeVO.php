<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 26.08.16
 * Time: 15:23
 */

namespace Oxa\DfpBundle\Model\DataType;

class DateRangeVO implements DateRangeInterface
{
    private $startDate;

    private $endDate;

    public function __construct(\DateTime $startDate, \DateTime $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function getStartDate()
    {
        return $this->startDate;
    }

    public function getEndDate()
    {
        return $this->endDate;
    }
}