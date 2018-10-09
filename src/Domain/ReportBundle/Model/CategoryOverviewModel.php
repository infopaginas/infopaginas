<?php

namespace Domain\ReportBundle\Model;

class CategoryOverviewModel implements ReportInterface
{
    const TYPE_CODE_IMPRESSION = 'impressions';
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
    public static function getBusinessEventTypes()
    {
        return [
            self::TYPE_CODE_IMPRESSION,
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

    /**
     * @return array
     */
    public static function getEventTypesByPriority()
    {
        return [
            self::EVENT_PRIORITY_MAIN => [
                self::TYPE_CODE_IMPRESSION,
            ],
            self::EVENT_PRIORITY_COMMON => [
                self::TYPE_CODE_CALL_MOB_BUTTON,
                self::TYPE_CODE_DIRECTION_BUTTON,
            ],
        ];
    }

    /**
     * @return array
     */
    public static function getChartEventTypes()
    {
        return [
            self::TYPE_CODE_IMPRESSION,
            self::TYPE_CODE_CALL_MOB_BUTTON,
            self::TYPE_CODE_DIRECTION_BUTTON,
        ];
    }

    /**
     * @return array
     */
    public static function getChartEventTypesWithTranslation()
    {
        $allowedEvents = self::getChartEventTypes();

        $result = array_intersect_key(self::EVENT_TYPES, array_flip($allowedEvents));

        return $result;
    }

    /**
     * @return array
     */
    public static function getAllChartEventTypesWithTranslation()
    {
        $allowedEvents = self::getChartEventTypes();

        $result = array_intersect_key(self::EVENT_TYPES, array_flip($allowedEvents));

        return $result;
    }

    /**
     * @return array
     */
    public static function getChartHints()
    {
        return [
            self::TYPE_CODE_IMPRESSION       => 'interaction_report.hint.impression',
            self::TYPE_CODE_CALL_MOB_BUTTON  => 'interaction_report.hint.call_mob',
            self::TYPE_CODE_DIRECTION_BUTTON => 'interaction_report.hint.direction',
        ];
    }

    /**
     * @param string $type
     *
     * @return string
     */
    public static function getChartHintByType($type)
    {
        if (!empty(self::getChartHints()[$type])) {
            $hint = self::getChartHints()[$type];
        } else {
            $hint = '';
        }

        return $hint;
    }

    /**
     * @return array
     */
    public static function getActionsUserData()
    {
        return [
            self::TYPE_CODE_IMPRESSION       => self::EVENT_TYPES[self::TYPE_CODE_IMPRESSION],
            self::TYPE_CODE_CALL_MOB_BUTTON  => 'Calls',
            self::TYPE_CODE_DIRECTION_BUTTON => 'Direction',
        ];
    }

    /**
     * @return array
     */
    public static function getActionTooltip()
    {
        return [
            self::TYPE_CODE_IMPRESSION       => 'user_profile.tooltip.impression',
            self::TYPE_CODE_CALL_MOB_BUTTON  => 'user_profile.tooltip.call_mob_button',
            self::TYPE_CODE_DIRECTION_BUTTON => 'user_profile.tooltip.direction_button',
        ];
    }
}
