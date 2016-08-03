<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 7/5/16
 * Time: 11:44 AM
 */

namespace Oxa\Sonata\AdminBundle\Util\Helpers;

use Domain\BusinessBundle\Util\Traits\StatusTrait;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Class AdminHelper
 * @package Oxa\Sonata\AdminBundle\Util\Helpers
 */
class AdminHelper
{
    const FILTER_DATE_FORMAT = 'd-m-Y';

    const PER_PAGE_ALL = 'all';

    const DATE_FORMAT = 'd.m.Y';
    const DATE_MONTH_FORMAT = 'm.Y';

    const DATE_RANGE_CODE_CUSTOM     = 'custom';
    const DATE_RANGE_CODE_TODAY      = 'today';
    const DATE_RANGE_CODE_LAST_WEEK  = 'last_week';
    const DATE_RANGE_CODE_LAST_MONTH = 'last_month';
    const DATE_RANGE_CODE_LAST_YEAR  = 'last_year';

    const PERIOD_OPTION_CODE_DAILY      = 'daily';
    const PERIOD_OPTION_CODE_PER_MONTH  = 'per_month';

    /**
     * @return array
     */
    public static function getDatagridStatusOptions()
    {
        return [
            'field_type' => 'choice',
            'field_options' => [
                'required'  => false,
                'choices'   => StatusTrait::getStatuses()
            ]
        ];
    }

    /**
     * Get period option values for choice form type
     * @return array
     */
    public static function getPeriodOptionValues()
    {
        return [
            self::PERIOD_OPTION_CODE_DAILY      => 'filter.label.period.daily',
            self::PERIOD_OPTION_CODE_PER_MONTH  => 'filter.label.period.per_month',
        ];
    }

    /**
     * Get date period values for choice form type
     * @return array
     */
    public static function getDatePeriodValues()
    {
        return [
            self::DATE_RANGE_CODE_TODAY      => 'filter.label.today',
            self::DATE_RANGE_CODE_LAST_WEEK  => 'filter.label.last_week',
            self::DATE_RANGE_CODE_LAST_MONTH => 'filter.label.last_month',
            self::DATE_RANGE_CODE_LAST_YEAR  => 'filter.label.last_year',
            self::DATE_RANGE_CODE_CUSTOM     => 'filter.label.custom',
        ];
    }

    /**
     * Get date range for each date code
     * @return array
     */
    public static function getDataPeriodParameters()
    {
        $datetimeDay    = new \DateTime();
        $datetimeWeek   = new \DateTime();
        $datetimeMonth  = new \DateTime();
        $datetimeYear   = new \DateTime();

        return [
            self::DATE_RANGE_CODE_TODAY => [
                'end'   => $datetimeDay->format(self::FILTER_DATE_FORMAT),
                'start' => $datetimeDay->format(self::FILTER_DATE_FORMAT),
            ],
            self::DATE_RANGE_CODE_LAST_WEEK  => [
                'end'   => $datetimeWeek->format(self::FILTER_DATE_FORMAT),
                'start' => $datetimeWeek->modify('-1 week')->modify('+1 day')->format(self::FILTER_DATE_FORMAT),
            ],
            self::DATE_RANGE_CODE_LAST_MONTH => [
                'end'   => $datetimeMonth->format(self::FILTER_DATE_FORMAT),
                'start' => $datetimeMonth->modify('-1 month')->modify('+1 day')->format(self::FILTER_DATE_FORMAT),
            ],
            self::DATE_RANGE_CODE_LAST_YEAR => [
                'end'   => $datetimeYear->format(self::FILTER_DATE_FORMAT),
                'start' => $datetimeYear->modify('-1 year')->modify('+1 day')->format(self::FILTER_DATE_FORMAT),
            ],
            self::DATE_RANGE_CODE_CUSTOM => [
                'end'   => '',
                'start' => '',
            ],
        ];
    }

    /**
     * @return array
     */
    public static function getDatagridDatePeriodOptions()
    {
        return [
            'field_type' => 'choice',
            'field_options' => [
                'mapped' => false,
                'required'  => true,
                'empty_value'  => false,
                'empty_data'  => self::DATE_RANGE_CODE_LAST_WEEK,
                'choices'   => self::getDatePeriodValues(),
                'translation_domain' => 'SonataAdminBundle'
            ],
        ];
    }

    /**
     * @return array
     */
    public static function getDatagridPeriodOptionOptions()
    {
        return [
            'field_type' => 'choice',
            'field_options' => [
                'mapped' => false,
                'required'  => true,
                'empty_value'  => false,
                'empty_data'  => self::PERIOD_OPTION_CODE_DAILY,
                'choices'   => self::getPeriodOptionValues(),
                'translation_domain' => 'SonataAdminBundle'
            ],
        ];
    }
}
