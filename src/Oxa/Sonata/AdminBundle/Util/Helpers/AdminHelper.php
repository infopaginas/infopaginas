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
    const DATE_FORMAT = 'd.m.Y';
    
    const DATE_RANGE_CODE_TODAY      = 'today';
    const DATE_RANGE_CODE_LAST_WEEK  = 'last_week';
    const DATE_RANGE_CODE_LAST_MONTH = 'last_month';

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
     * Get date period values for choice form type
     * @return array
     */
    public static function getDatePeriodValues()
    {
        return [
            self::DATE_RANGE_CODE_TODAY      => 'filter.label.today',
            self::DATE_RANGE_CODE_LAST_WEEK  => 'filter.label.last_week',
            self::DATE_RANGE_CODE_LAST_MONTH => 'filter.label.last_month',
        ];
    }

    /**
     * Get date range for each date code
     * @return array
     */
    public static function getDataPeriodParameters()
    {
        $datetimeDay = new \DateTime();
        $datetimeWeek = clone $datetimeDay;
        $datetimeMonth = clone $datetimeDay;

        return [
            self::DATE_RANGE_CODE_TODAY => array(
                'end'   => $datetimeDay->format('d-m-Y'),
                'start' => $datetimeDay->modify('-1 day')->format('d-m-Y'),
            ),
            self::DATE_RANGE_CODE_LAST_WEEK  => [
                'end'   => $datetimeWeek->format('d-m-Y'),
                'start' => $datetimeWeek->modify('-1 week')->format('d-m-Y'),
            ],
            self::DATE_RANGE_CODE_LAST_MONTH => [
                'end'   => $datetimeMonth->format('d-m-Y'),
                'start' => $datetimeMonth->modify('-1 month')->format('d-m-Y'),
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
                'required'  => false,
                'choices'   => self::getDatePeriodValues(),
                'translation_domain' => 'SonataAdminBundle'
            ],
        ];
    }
}
