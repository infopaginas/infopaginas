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

    const FILTER_DATE_RANGE_FORMAT = 'dd-MM-y';

    const PER_PAGE_ALL = 'all';

    const DATE_FORMAT = 'd.m.Y';
    const DATE_MONTH_FORMAT = 'm.Y';
    const DATETIME_FORMAT = 'd.m.Y H:i:s';

    const DATE_RANGE_CODE_CUSTOM     = 'custom';
    const DATE_RANGE_CODE_TODAY      = 'today';
    const DATE_RANGE_CODE_LAST_WEEK  = 'last_week';
    const DATE_RANGE_CODE_LAST_MONTH = 'last_month';
    const DATE_RANGE_CODE_LAST_YEAR  = 'last_year';

    const PERIOD_OPTION_CODE_DAILY      = 'daily';
    const PERIOD_OPTION_CODE_PER_MONTH  = 'per_month';

    const FILTER_DATE_RANGE_CLASS   = 'oxa_filter_date_range';
    const FILTER_DATE_PERIOD_CLASS  = 'oxa_filter_date_period';

    const MAX_IMAGE_FILESIZE = '10M';

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
        // todo: all datePeriod filter logic should be refactored or removed
        // as it has been cut during sonata update as a result of logic conflict
        return [
            'field_type' => 'choice',
            'field_options' => [
                'mapped' => false,
                'required'  => true,
                'empty_value'  => false,
                'empty_data'  => self::DATE_RANGE_CODE_LAST_WEEK,
                'choices'   => self::getDatePeriodValues(),
                'translation_domain' => 'SonataAdminBundle',
                'attr' => [
                    'class' => self::FILTER_DATE_PERIOD_CLASS
                ]
            ],
        ];
    }

    /**
     * @return array
     */
    public static function getDatagridPeriodOptionOptions()
    {
        return [
            'show_filter' => true,
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

    /**
     * Used to set default datetime options
     *
     * @return array
     */
    public static function getDatagridDateTypeOptions()
    {
        return [
            'field_type' => 'sonata_type_datetime_range_picker',
            'field_options' => [
                'field_options' => [
                    'format' => self::FILTER_DATE_RANGE_FORMAT
                ],
                'attr' => [
                    'class' => self::FILTER_DATE_RANGE_CLASS
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public static function getFormImageFileConstrain()
    {
        return [
            'maxSize' => self::MAX_IMAGE_FILESIZE,
            'mimeTypes' => [
                "image/png",
                "image/jpeg",
                "image/jpg",
                "image/gif",
            ]
        ];
    }
}
