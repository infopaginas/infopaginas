<?php

namespace Oxa\Sonata\AdminBundle\Util\Helpers;

use Domain\BusinessBundle\Entity\BusinessProfilePopup;
use Domain\BusinessBundle\Util\Traits\StatusTrait;
use Sonata\Form\Type\DateTimeRangePickerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Class AdminHelper
 * @package Oxa\Sonata\AdminBundle\Util\Helpers
 */
class AdminHelper
{
    public const FILTER_DATE_FORMAT = 'd-m-Y';

    public const FILTER_DATE_RANGE_FORMAT = 'dd-MM-y';

    public const PER_PAGE_ALL = 'all';

    public const DATE_FORMAT            = 'm/d/Y';
    public const DATE_WEEK_FORMAT       = 'W/Y';
    public const DATE_MONTH_FORMAT      = 'm/Y';
    public const DATETIME_FORMAT        = 'm/d/Y H:i:s';
    public const DATE_FULL_MONTH_FORMAT = 'F, Y';

    public const DATE_RANGE_CODE_CUSTOM     = 'custom';
    public const DATE_RANGE_CODE_TODAY      = 'today';
    public const DATE_RANGE_CODE_LAST_WEEK  = 'last_week';
    public const DATE_RANGE_CODE_LAST_MONTH = 'last_month';
    public const DATE_RANGE_CODE_LAST_YEAR  = 'last_year';

    public const PERIOD_OPTION_CODE_DAILY      = 'daily';
    public const PERIOD_OPTION_CODE_WEEKLY     = 'weekly';
    public const PERIOD_OPTION_CODE_PER_MONTH  = 'per_month';

    public const FILTER_DATE_RANGE_CLASS   = 'oxa_filter_date_range';
    public const FILTER_DATE_PERIOD_CLASS  = 'oxa_filter_date_period';

    public const MAX_IMAGE_FILESIZE = '10M';
    public const MAX_VIDEO_FILESIZE = '128M';
    public const MAX_BUSINESS_PROFILE_POPUP_FILESIZE = '5M';

    public const MAX_PAGE_RANGE_SIZE = 25;

    /**
     * @return array
     */
    public static function getDatagridStatusOptions()
    {
        return [
            'field_type' => ChoiceType::class,
            'field_options' => [
                'required'  => false,
                'choices'   => array_flip(StatusTrait::getStatuses()),
            ]
        ];
    }

    public static function getVideoDatagridStatusOptions()
    {
        return [
            'field_type' => ChoiceType::class,
            'field_options' => [
                'required'  => false,
                'choices'   => array_flip(StatusTrait::getVideoStatuses()),
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
            self::PERIOD_OPTION_CODE_WEEKLY     => 'filter.label.period.weekly',
            self::PERIOD_OPTION_CODE_PER_MONTH  => 'filter.label.period.per_month',
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
    public static function getDatagridPeriodOptionOptions()
    {
        return [
            'show_filter' => true,
            'field_type' => ChoiceType::class,
            'field_options' => [
                'mapped' => false,
                'required'  => true,
                'empty_data'  => self::PERIOD_OPTION_CODE_DAILY,
                'choices'   => array_flip(self::getPeriodOptionValues()),
                'translation_domain' => 'SonataAdminBundle'
            ],
        ];
    }

    /**
     * Used to set report options
     *
     * @return array
     */
    public static function getReportDateTypeOptions()
    {
        return [
            'show_filter' => true,
            'field_type'  => DateTimeRangePickerType::class,
            'field_options' => [
                'field_options' => [
                    'format'        => self::FILTER_DATE_RANGE_FORMAT,
                ],
                'attr' => [
                    'class' => self::FILTER_DATE_RANGE_CLASS,
                ],
                'mapped'    => false,
                'required'  => true,
            ]
        ];
    }

    /**
     * @return array
     */
    public static function getFormImageFileConstrain()
    {
        return [
            'mimeTypes' => [
                "image/png",
                "image/jpeg",
                "image/jpg",
                "image/gif",
            ]
        ];
    }

    /**
     * @return array
     */
    public static function getFormImageFileAccept()
    {
        return [
            'jpg',
            'png',
            'gif',
            'image/jpeg',
            'image/pjpeg',
            'image/png',
            'image/gif',
        ];
    }

    /**
     * @return array
     */
    public static function getFormCSVFileAccept()
    {
        return [
            '.csv',
        ];
    }

    /**
     * @return array
     */
    public static function getFormVideoFileConstrain()
    {
        return [
            'maxSize' => self::MAX_VIDEO_FILESIZE,
            'mimeTypes' => [
                'video/mp4',
                'video/webm',
                'video/ogg',
                'video/quicktime',
                'video/avi',
                'video/mpeg',
                'video/x-ms-wmv',
                'video/x-flv',
            ]
        ];
    }

    public static function getFormPopupFileConstrain(): array
    {
        return [
            'maxSize' => self::MAX_BUSINESS_PROFILE_POPUP_FILESIZE,
            'mimeTypes' => [
                BusinessProfilePopup::FILE_MIME_TYPE,
            ]
        ];
    }

    /**
     * @return array
     */
    public static function getAccentedChars()
    {
        return [
            'á' => 'a',
            'à' => 'a',
            'â' => 'a',
            'ã' => 'a',
            'ä' => 'a',
            'å' => 'a',
            'é' => 'e',
            'è' => 'e',
            'ê' => 'e',
            'ë' => 'e',
            'í' => 'i',
            'ì' => 'i',
            'î' => 'i',
            'ï' => 'i',
            'ñ' => 'n',
            'ó' => 'o',
            'ò' => 'o',
            'ô' => 'o',
            'õ' => 'o',
            'ö' => 'o',
            'ú' => 'u',
            'ù' => 'u',
            'ü' => 'u',
            'û' => 'u',
            'ý' => 'y',
            'ÿ' => 'y',
            'ç' => 'c',
            'ß' => 'ss',
            'æ' => 'ae',
            'œ' => 'oe',
            '\s+' => ' ',
        ];
    }

    /**
     * @param $value string
     *
     * @return string
     */
    public static function convertAccentedString($value)
    {
        $string = mb_strtolower($value);

        $accentedChars = self::getAccentedChars();

        $string = str_replace(array_keys($accentedChars), array_values($accentedChars), $string);

        return $string;
    }

    /**
     * @param int $currentPage
     * @param int $lastPage
     *
     * @return array
     */
    public static function getPageRanges($currentPage, $lastPage)
    {
        $firstPage = 1;

        $startPage = $firstPage;
        $endPage   = $lastPage;

        $range = floor(self::MAX_PAGE_RANGE_SIZE/2);

        if ($currentPage - $firstPage > $range) {
            $startPage = $currentPage - $range;
        }

        if ($lastPage - $currentPage > $range) {
            $endPage = $currentPage + $range;
        }

        $pageRange = range($startPage, $endPage);

        return $pageRange;
    }
}
