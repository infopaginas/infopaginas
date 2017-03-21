<?php

namespace Domain\ReportBundle\Util;

use Domain\ReportBundle\Model\DataType\ReportDatesRangeVO;
use Oxa\DfpBundle\Model\DataType\DateRangeVO;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;

/**
 * Class DatesUtil
 * @package Domain\ReportBundle\Util
 */
class DatesUtil
{
    const STEP_DAY = '+1 day';

    const STEP_MONTH = '+1 month';

    const DEFAULT_PERIOD = '-30 days';

    const RANGE_TODAY = 'today';
    const RANGE_THIS_WEEK = 'this_week';
    const RANGE_LAST_WEEK = 'last_week';
    const RANGE_THIS_MONTH = 'this_month';
    const RANGE_LAST_MONTH = 'last_month';
    const RANGE_CUSTOM = 'custom';

    const RANGE_YESTERDAY = 'yesterday';
    const RANGE_LAST_HOUR = 'last_hour';

    const RANGE_THIS_YEAR = 'this_year';
    const RANGE_LAST_YEAR = 'last_year';

    const RANGE_DEFAULT = 'this_week';

    const START_END_DATE_ARRAY_FORMAT = 'd-m-Y';
    const DATE_DB_FORMAT              = 'Y-m-d';

    public static function getReportDataRanges()
    {
        return [
            self::RANGE_TODAY      => 'Today',
            self::RANGE_THIS_WEEK  => 'This week',
            self::RANGE_LAST_WEEK  => 'Last week',
            self::RANGE_THIS_MONTH => 'This month',
            self::RANGE_LAST_MONTH => 'Last month',
            self::RANGE_CUSTOM     => 'Custom',
        ];
    }

    public static function getDateRangeValueObjectFromRangeType(string $range)
    {
        switch ($range) {
            case self::RANGE_TODAY:
                $start = new \DateTime('today 00:00:00');
                $end = new \DateTime('today  23:59:59');
                break;
            case self::RANGE_THIS_WEEK:
                $start = new \DateTime('monday this week');
                $end = new \DateTime('sunday this week');
                break;
            case self::RANGE_LAST_WEEK:
                $start = new \DateTime('monday last week');
                $end = new \DateTime('monday this week - 1 second');
                break;
            case self::RANGE_THIS_MONTH:
                $start = new \DateTime('first day of this month');
                $end = new \DateTime('last day of this month');
                break;
            case self::RANGE_LAST_MONTH:
                $start = new \DateTime('first day of last month');
                $end = new \DateTime('last day of last month');
                break;
            case self::RANGE_THIS_YEAR:
                $start = new \DateTime('first day of January ' . date('Y'));
                $end = new \DateTime('last day of December ' . date('Y'));
                break;
            case self::RANGE_LAST_YEAR:
                $start = new \DateTime('first day of January ' . (date('Y') -1));
                $end = new \DateTime('last day of December ' . (date('Y') - 1));
                break;
            case self::RANGE_YESTERDAY:
                $start = new \DateTime('yesterday 00:00:00');
                $end = new \DateTime('yesterday 23:59:59');
                break;
            case self::RANGE_LAST_HOUR:
                $start = new \DateTime('last hour');
                $start->setTime($start->format('G'), 0);

                $end = new \DateTime('this hour');
                $end->setTime($end->format('G'), 0);
                break;
            default:
                throw new \Exception('invalid param');
        }

        return new DateRangeVO($start, $end);
    }

    public static function getDateAsArrayFromVO(DateRangeVO $dateRange)
    {
        $date['start'] = $dateRange->getStartDate()->format(self::START_END_DATE_ARRAY_FORMAT);
        $date['end']   = $dateRange->getEndDate()->format(self::START_END_DATE_ARRAY_FORMAT);

        return $date;
    }

    public static function getDateAsDateRangeVOFromRequestData(array $requestData, string $dateFormat)
    {
        $start = \DateTime::createFromFormat($dateFormat, $requestData['start']);
        $end = \DateTime::createFromFormat($dateFormat, $requestData['end']);

        return new DateRangeVO($start, $end);
    }

    public static function getDateRangeVOFromDateString(string $start, string $end) : ReportDatesRangeVO
    {
        $startDate = \DateTime::createFromFormat(DatesUtil::START_END_DATE_ARRAY_FORMAT, $start);
        $endDate = \DateTime::createFromFormat(DatesUtil::START_END_DATE_ARRAY_FORMAT, $end);

        $startDate->setTime(0, 0, 0);
        $endDate->setTime(23, 59, 59);

        return new ReportDatesRangeVO($startDate, $endDate);
    }

    public static function getDateAsArrayFromRequestData(array $requestData, string $format = self::DATE_DB_FORMAT)
    {
        $date['start'] = \DateTime::createFromFormat($format, $requestData['start'])
            ->format(self::START_END_DATE_ARRAY_FORMAT);

        $date['end']   = \DateTime::createFromFormat($format, $requestData['end'])
            ->format(self::START_END_DATE_ARRAY_FORMAT);

        return $date;
    }

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
        $rangeVO,
        string $step = '+1 day',
        string $outputFormat = 'd.m.Y'
    ) : array {
        $dates = [];

        if ($step == self::STEP_DAY) {
            $dateFrom = clone $rangeVO->getStartDate();
            $dateTo   = clone $rangeVO->getEndDate();
        } else {
            $dateFrom = clone $rangeVO->getStartDate();
            $dateFrom->modify('first day of this month');

            $dateTo = clone $rangeVO->getEndDate();
            $dateTo->modify('last day of this month');
        }

        $interval = \DateInterval::createFromDateString($step);
        $period   = new \DatePeriod($dateFrom, $interval, $dateTo);

        foreach ($period as $date) {
            $dates[] = $date->format($outputFormat);
        }

        return $dates;
    }

    protected static function normalizeMonthNumber($timestamp)
    {
        return date('n', $timestamp) - 1;
    }

    public static function isValidDateString($dateString, $dateFormat = self::START_END_DATE_ARRAY_FORMAT)
    {
        try {
            return \DateTime::createFromFormat($dateFormat, $dateString);
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getThisWeekStart()
    {
        return new \DateTime('monday this week');
    }

    public static function getThisWeekEnd()
    {
        return new \DateTime('sunday this week');
    }

    public static function getYesterday()
    {
        return new \DateTime('yesterday');
    }

    public static function convertMonthlyFormattedDate($date, $format)
    {
        $newDate = \DateTime::createFromFormat(AdminHelper::DATE_MONTH_FORMAT, $date);

        if ($newDate) {
            return $newDate->format($format);
        }

        return $date;
    }
}