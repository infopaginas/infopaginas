<?php

namespace Domain\ReportBundle\Util;

use Domain\ReportBundle\Model\DataType\ReportDatesRangeVO;
use Google\AdsApi\Dfp\v201702\DateRangeType;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;

/**
 * Class DatesUtil
 * @package Domain\ReportBundle\Util
 */
class DatesUtil
{
    const STEP_DAY   = '+1 day';
    const STEP_WEEK  = '+7 days';
    const STEP_MONTH = '+1 month';

    const DEFAULT_PERIOD = '-30 days';

    const RANGE_TODAY = 'today';
    const RANGE_THIS_WEEK = 'this_week';
    const RANGE_LAST_WEEK = 'last_week';
    const RANGE_THIS_MONTH = 'this_month';
    const RANGE_LAST_MONTH = 'last_month';
    const RANGE_LAST_3_MONTH  = 'last_3_month';
    const RANGE_LAST_6_MONTH  = 'last_6_month';
    const RANGE_LAST_12_MONTH = 'last_12_month';
    const RANGE_LAST_30_DAYS = 'last_30_days';
    const RANGE_CUSTOM = 'custom';

    const RANGE_YESTERDAY = 'yesterday';
    const RANGE_LAST_HOUR = 'last_hour';

    const RANGE_THIS_YEAR = 'this_year';
    const RANGE_LAST_YEAR = 'last_year';

    const RANGE_DEFAULT = 'this_week';

    const START_END_DATE_ARRAY_FORMAT = 'd-m-Y';
    const DATE_DB_FORMAT              = 'Y-m-d';

    /**
     * @return array
     */
    public static function getReportDataRanges()
    {
        $ranges = self::getReportAdminDataRanges();

        $ranges[self::RANGE_CUSTOM] = 'business_profile.interaction_chart.period.custom';

        return $ranges;
    }

    /**
     * @return array
     */
    public static function getReportAdminDataRanges()
    {
        return [
            self::RANGE_LAST_MONTH    => 'business_profile.interaction_chart.period.last_month',
            self::RANGE_LAST_3_MONTH  => 'business_profile.interaction_chart.period.last_3_month',
            self::RANGE_LAST_6_MONTH  => 'business_profile.interaction_chart.period.last_6_month',
            self::RANGE_LAST_12_MONTH => 'business_profile.interaction_chart.period.last_12_month',
        ];
    }

    /**
     * @param string $range
     *
     * @return ReportDatesRangeVO
     * @throws \Exception
     */
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
            case self::RANGE_LAST_30_DAYS:
                list($start, $end) = self::getCurrentDatetimePeriod();
                $start->modify('-30 days');
                break;
            case self::RANGE_LAST_MONTH:
                list($start, $end) = self::getCurrentDatetimePeriod();
                $start->modify('-1 month');
                break;
            case self::RANGE_LAST_3_MONTH:
                list($start, $end) = self::getCurrentDatetimePeriod();
                $start->modify('-3 month');
                break;
            case self::RANGE_LAST_6_MONTH:
                list($start, $end) = self::getCurrentDatetimePeriod();
                $start->modify('-6 month');
                break;
            case self::RANGE_LAST_12_MONTH:
                list($start, $end) = self::getCurrentDatetimePeriod();
                $start->modify('-12 month');
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

        return new ReportDatesRangeVO($start, $end);
    }

    /**
     * @param ReportDatesRangeVO $dateRange
     *
     * @return array
     */
    public static function getDateAsArrayFromVO(ReportDatesRangeVO $dateRange)
    {
        $date['start'] = $dateRange->getStartDate()->format(self::START_END_DATE_ARRAY_FORMAT);
        $date['end']   = $dateRange->getEndDate()->format(self::START_END_DATE_ARRAY_FORMAT);

        return $date;
    }

    /**
     * @param array $requestData
     * @param string $dateFormat
     *
     * @return ReportDatesRangeVO
     */
    public static function getDateAsDateRangeVOFromRequestData(array $requestData, string $dateFormat)
    {
        $start = \DateTime::createFromFormat($dateFormat, $requestData['start']);
        $end = \DateTime::createFromFormat($dateFormat, $requestData['end']);

        return new ReportDatesRangeVO($start, $end);
    }

    /**
     * @param string $start
     * @param string $end
     *
     * @return ReportDatesRangeVO
     */
    public static function getDateRangeVOFromDateString(string $start, string $end) : ReportDatesRangeVO
    {
        $startDate = \DateTime::createFromFormat(DatesUtil::START_END_DATE_ARRAY_FORMAT, $start);
        $endDate = \DateTime::createFromFormat(DatesUtil::START_END_DATE_ARRAY_FORMAT, $end);

        $startDate->setTime(0, 0, 0);
        $endDate->setTime(23, 59, 59);

        return new ReportDatesRangeVO($startDate, $endDate);
    }

    /**
     * @param array $requestData
     * @param string $dateFormat
     *
     * @return array
     */
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
        } elseif ($step == self::STEP_WEEK) {
            $dateFrom = clone $rangeVO->getStartDate();
            $dateFrom->modify('this week');

            $dateTo = clone $rangeVO->getEndDate();
            $dateTo->modify('this week +6 days');
        } else {
            $dateFrom = clone $rangeVO->getStartDate();
            $dateFrom->modify('first day of this month');

            $dateTo = clone $rangeVO->getEndDate();
            $dateTo->modify('last day of this month');
        }

        $interval = \DateInterval::createFromDateString($step);
        $period   = new \DatePeriod($dateFrom, $interval, $dateTo);

        foreach ($period as $date) {
            if ($outputFormat == AdminHelper::DATE_WEEK_FORMAT) {
                $formattedDate = self::getWeeklyFormatterDate($date);
            } else {
                $formattedDate = $date->format($outputFormat);
            }

            $dates[] = $formattedDate;
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

    /**
     * @return \DateTime
     */
    public static function getThisWeekStart()
    {
        return new \DateTime('monday this week');
    }

    /**
     * @return \DateTime
     */
    public static function getThisWeekEnd()
    {
        return new \DateTime('sunday this week');
    }

    /**
     * @return \DateTime
     */
    public static function getYesterday()
    {
        return new \DateTime('yesterday');
    }

    /**
     * @return \DateTime
     */
    public static function getToday()
    {
        return new \DateTime('today');
    }

    /**
     * @param string $period
     *
     * @return \Datetime
     */
    public static function getAdUsageReportDateByPeriod($period)
    {
        switch ($period) {
            case DateRangeType::YESTERDAY:
                $date = self::getYesterday();
                break;
            default:
                $date = self::getToday();
                break;
        }

        return $date;
    }

    /**
     * @param string $period
     *
     * @return string
     */
    public static function getAdUsageReportRangeByPeriod($period)
    {
        switch ($period) {
            case DateRangeType::YESTERDAY:
                $range = self::RANGE_YESTERDAY;
                break;
            default:
                $range = self::RANGE_TODAY;
                break;
        }

        return $range;
    }

    /**
     * @param string $date
     * @param string $format
     *
     * @return string
     */
    public static function convertMonthlyFormattedDate($date, $format)
    {
        $newDate = \DateTime::createFromFormat(AdminHelper::DATE_MONTH_FORMAT, $date);

        if ($newDate) {
            return $newDate->format($format);
        }

        return $date;
    }

    /**
     * @param $mongoDateTime MongoDB\BSON\UTCDateTime
     *
     * @return \Datetime
     */
    public static function convertMongoDbTimeToDatetime($mongoDateTime)
    {
        /** @var $datetime \Datetime */
        $datetime = $mongoDateTime->toDateTime();
        $datetime->setTimezone(new \DateTimeZone(date_default_timezone_get()));

        return $datetime;
    }

    /**
     * @param \Datetime $datetime
     *
     * @return \Datetime
     */
    public static function setDayStart($datetime)
    {
        // set time to day start (0h 0m 0s)
        $datetime->setTime(0, 0, 0);

        return $datetime;
    }

    /**
     * @param \Datetime $datetime
     *
     * @return \Datetime
     */
    public static function setDayEnd($datetime)
    {
        // set time to day end (23h 59m 59s)
        $datetime->setTime(23, 59, 59);

        return $datetime;
    }

    /**
     * @param \Datetime $date
     *
     * @return string
     */
    public static function getWeeklyFormatterDate($date)
    {
        $formattedDate = sprintf(
            '%s - %s',
            $date->modify('this week')->format(AdminHelper::DATE_FORMAT),
            $date->modify('this week +6 days')->format(AdminHelper::DATE_FORMAT)
        );

        return $formattedDate;
    }

    /**
     * @return array
     */
    public static function getCurrentDatetimePeriod()
    {
        $start = new \DateTime();
        $end   = clone $start;

        return [
            $start,
            $end
        ];
    }
}
