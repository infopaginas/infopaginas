<?php

namespace Domain\ReportBundle\Model;

class CategoryOverviewModel implements ReportInterface
{
    const TYPE_CODE_IMPRESSION             = 'impressions';
    const TYPE_CODE_DIRECTION_BUTTON       = 'directionButton';
    const TYPE_CODE_CALL_MOB_BUTTON        = 'callMobButton';

    const EVENT_TYPES = [
        self::TYPE_CODE_IMPRESSION            => 'interaction_report.event.impression',
        self::TYPE_CODE_DIRECTION_BUTTON      => 'interaction_report.button.direction',
        self::TYPE_CODE_CALL_MOB_BUTTON       => 'interaction_report.button.call_mob',
    ];

    const EVENT_PRIORITY_MAIN   = 'main';
    const EVENT_PRIORITY_COMMON = 'common';
    const EVENT_PRIORITY_HIDDEN = 'hidden';

    const DEFAULT_CHART_TYPE = self::TYPE_CODE_IMPRESSION;

    /**
     * @return array
     */
    public static function getExportFormats()
    {
        return [
            self::CODE_PDF_BUSINESS_OVERVIEW_REPORT   => self::FORMAT_PDF,
            self::CODE_EXCEL_BUSINESS_OVERVIEW_REPORT => self::FORMAT_EXCEL,
        ];
    }

    /**
     * @return array
     */
    public static function getTypes()
    {
        return [
            self::TYPE_CODE_IMPRESSION,
            self::TYPE_CODE_DIRECTION_BUTTON,
            self::TYPE_CODE_CALL_MOB_BUTTON,
        ];
    }
}
