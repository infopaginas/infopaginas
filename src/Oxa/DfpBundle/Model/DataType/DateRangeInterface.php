<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 26.08.16
 * Time: 15:25
 */

namespace Oxa\DfpBundle\Model\DataType;

interface DateRangeInterface
{
    public function __construct(\DateTime $startDate, \DateTime $endDate);

    public function getStartDate();

    public function getEndDate();
}
