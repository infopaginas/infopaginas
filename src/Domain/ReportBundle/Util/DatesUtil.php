<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 04.09.16
 * Time: 14:58
 */

namespace Domain\ReportBundle\Util;

use Domain\ReportBundle\Model\DataType\ReportDatesRangeVO;

/**
 * Class DatesUtil
 * @package Domain\ReportBundle\Util
 */
class DatesUtil
{
    const STEP_DAY = '+1 day';

    const STEP_MONTH = '+1 month';

    /**
     *
     * Creating date collection between two dates
     *
     * <code>
     * <?php
     * # Example 1
     * ::dateRange("2014-01-01", "2014-01-20", "+1 day", "m/d/Y");
     *
     * # Example 2. you can use even time
     * ::dateRange("01:00:00", "23:00:00", "+1 hour", "H:i:s");
     * </code>
     *
     * @param ReportDatesRangeVO $rangeVO
     * @param string $step
     * @param string $outputFormat
     * @return array
     */
    public static function dateRange(
        ReportDatesRangeVO $rangeVO,
        string $step = '+1 day',
        string $outputFormat = 'd.m.Y'
    ) : array {
        $dates = [];

        $from = $rangeVO->getStartDate()->getTimestamp();
        $to = $rangeVO->getEndDate()->getTimestamp();

        while ($from <= $to) {
            $dates[] = date($outputFormat, $from);
            $from = strtotime($step, $from);
        }

        $dates[] = date($outputFormat, $from);

        return $dates;
    }

    protected static function normalizeMonthNumber($timestamp)
    {
        return date('n', $timestamp) - 1;
    }
}