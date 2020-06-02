<?php

namespace Domain\ReportBundle\Model;

class BusinessOverviewModel implements ReportInterface
{
    public const TYPE_CODE_IMPRESSION        = 'impressions';
    public const TYPE_CODE_VIEW              = 'views';
    public const TYPE_CODE_KEYWORD           = 'keyword';
    public const TYPE_CODE_ADS               = 'ads';
    public const TYPE_CODE_SOCIAL_NETWORKS   = 'social_networks';
    public const TYPE_CODE_CATEGORY_BUSINESS = 'category_business';
    public const TYPE_CODE_CATEGORY_CATALOG  = 'category_catalog';
    public const TYPE_CODE_GEOLOCATION       = 'geolocation';

    public const TYPE_CODE_DIRECTION_BUTTON       = 'directionButton';
    public const TYPE_CODE_MAP_SHOW_BUTTON        = 'mapShowButton';
    public const TYPE_CODE_MAP_MARKER_BUTTON      = 'mapMarkerButton';
    public const TYPE_CODE_WEB_BUTTON             = 'webButton';
    public const TYPE_CODE_WEB_ACTION_BUTTON      = 'webActionButton';
    public const TYPE_CODE_CALL_MOB_BUTTON        = 'callMobButton';
    public const TYPE_CODE_CALL_DESK_BUTTON       = 'callDeskButton';
    public const TYPE_CODE_ADD_COMPARE_BUTTON     = 'addCompareButton';
    public const TYPE_CODE_REMOVE_COMPARE_BUTTON  = 'removeCompareButton';

    public const TYPE_CODE_FACEBOOK_SHARE  = 'facebookShare';
    public const TYPE_CODE_TWITTER_SHARE   = 'twitterShare';

    public const TYPE_CODE_LINKED_IN_VISIT    = 'linkedInVisit';
    public const TYPE_CODE_FACEBOOK_VISIT     = 'facebookVisit';
    public const TYPE_CODE_TWITTER_VISIT      = 'twitterVisit';
    public const TYPE_CODE_GOOGLE_VISIT       = 'googleVisit';
    public const TYPE_CODE_YOUTUBE_VISIT      = 'youtubeVisit';
    public const TYPE_CODE_INSTAGRAM_VISIT    = 'instagramVisit';
    public const TYPE_CODE_TRIP_ADVISOR_VISIT = 'tripAdvisorVisit';

    public const TYPE_CODE_VIDEO_WATCHED  = 'videoWatched';
    public const TYPE_CODE_REVIEW_CLICK   = 'reviewClick';
    public const TYPE_CODE_EMAIL_CLICK    = 'emailClick';

    public const EVENT_TYPES = [
        self::TYPE_CODE_VIEW                  => 'interaction_report.event.view',
        self::TYPE_CODE_IMPRESSION            => 'interaction_report.event.impression',
        self::TYPE_CODE_DIRECTION_BUTTON      => 'interaction_report.button.direction',
        self::TYPE_CODE_MAP_SHOW_BUTTON       => 'interaction_report.button.show_map',
        self::TYPE_CODE_MAP_MARKER_BUTTON     => 'interaction_report.button.marker_map',
        self::TYPE_CODE_WEB_BUTTON            => 'interaction_report.button.web',
        self::TYPE_CODE_WEB_ACTION_BUTTON     => 'interaction_report.button.web_action',
        self::TYPE_CODE_CALL_MOB_BUTTON       => 'interaction_report.button.call_mob',
        self::TYPE_CODE_CALL_DESK_BUTTON      => 'interaction_report.button.call_desk',
        self::TYPE_CODE_ADD_COMPARE_BUTTON    => 'interaction_report.button.add_compare',
        self::TYPE_CODE_REMOVE_COMPARE_BUTTON => 'interaction_report.button.remove_compare',
        self::TYPE_CODE_FACEBOOK_SHARE        => 'interaction_report.share.facebook',
        self::TYPE_CODE_TWITTER_SHARE         => 'interaction_report.share.twitter',
        self::TYPE_CODE_LINKED_IN_VISIT       => 'interaction_report.visit.linked_in',
        self::TYPE_CODE_FACEBOOK_VISIT        => 'interaction_report.visit.facebook',
        self::TYPE_CODE_TWITTER_VISIT         => 'interaction_report.visit.twitter',
        self::TYPE_CODE_GOOGLE_VISIT          => 'interaction_report.visit.google',
        self::TYPE_CODE_YOUTUBE_VISIT         => 'interaction_report.visit.youtube',
        self::TYPE_CODE_INSTAGRAM_VISIT       => 'interaction_report.visit.instagram',
        self::TYPE_CODE_TRIP_ADVISOR_VISIT    => 'interaction_report.visit.tripadvisor',
        self::TYPE_CODE_VIDEO_WATCHED         => 'interaction_report.video.watched',
        self::TYPE_CODE_REVIEW_CLICK          => 'interaction_report.review.click',
        self::TYPE_CODE_EMAIL_CLICK           => 'interaction_report.email.click',
    ];

    public const DEFAULT_CHART_TYPE = self::TYPE_CODE_IMPRESSION;

    private const EVENT_PRIORITY_MAIN   = 'main';
    private const EVENT_PRIORITY_COMMON = 'common';
    private const EVENT_PRIORITY_HIDDEN = 'hidden';

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
            self::TYPE_CODE_VIEW,
        ];
    }

    /**
     * @return array
     */
    public static function getTypes()
    {
        return [
            self::TYPE_CODE_IMPRESSION,
            self::TYPE_CODE_VIEW,
            self::TYPE_CODE_DIRECTION_BUTTON,
            self::TYPE_CODE_MAP_SHOW_BUTTON,
            self::TYPE_CODE_MAP_MARKER_BUTTON,
            self::TYPE_CODE_WEB_BUTTON,
            self::TYPE_CODE_WEB_ACTION_BUTTON,
            self::TYPE_CODE_CALL_MOB_BUTTON,
            self::TYPE_CODE_CALL_DESK_BUTTON,
            self::TYPE_CODE_ADD_COMPARE_BUTTON,
            self::TYPE_CODE_REMOVE_COMPARE_BUTTON,
            self::TYPE_CODE_FACEBOOK_SHARE,
            self::TYPE_CODE_TWITTER_SHARE,
            self::TYPE_CODE_LINKED_IN_VISIT,
            self::TYPE_CODE_FACEBOOK_VISIT,
            self::TYPE_CODE_TWITTER_VISIT,
            self::TYPE_CODE_GOOGLE_VISIT,
            self::TYPE_CODE_YOUTUBE_VISIT,
            self::TYPE_CODE_INSTAGRAM_VISIT,
            self::TYPE_CODE_TRIP_ADVISOR_VISIT,
            self::TYPE_CODE_VIDEO_WATCHED,
            self::TYPE_CODE_REVIEW_CLICK,
            self::TYPE_CODE_EMAIL_CLICK,
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
                self::TYPE_CODE_VIEW,
            ],
            self::EVENT_PRIORITY_COMMON => [
                self::TYPE_CODE_CALL_MOB_BUTTON,
                self::TYPE_CODE_CALL_DESK_BUTTON,
                self::TYPE_CODE_DIRECTION_BUTTON,
                self::TYPE_CODE_VIDEO_WATCHED,
            ],
            self::EVENT_PRIORITY_HIDDEN => [
                self::TYPE_CODE_MAP_SHOW_BUTTON,
                self::TYPE_CODE_MAP_MARKER_BUTTON,
                self::TYPE_CODE_WEB_BUTTON,
                self::TYPE_CODE_WEB_ACTION_BUTTON,
                self::TYPE_CODE_ADD_COMPARE_BUTTON,
                self::TYPE_CODE_REMOVE_COMPARE_BUTTON,
                self::TYPE_CODE_FACEBOOK_SHARE,
                self::TYPE_CODE_TWITTER_SHARE,
                self::TYPE_CODE_LINKED_IN_VISIT,
                self::TYPE_CODE_FACEBOOK_VISIT,
                self::TYPE_CODE_TWITTER_VISIT,
                self::TYPE_CODE_GOOGLE_VISIT,
                self::TYPE_CODE_YOUTUBE_VISIT,
                self::TYPE_CODE_INSTAGRAM_VISIT,
                self::TYPE_CODE_TRIP_ADVISOR_VISIT,
                self::TYPE_CODE_REVIEW_CLICK,
                self::TYPE_CODE_EMAIL_CLICK,
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
            self::TYPE_CODE_VIEW,
            self::TYPE_CODE_CALL_MOB_BUTTON,
            self::TYPE_CODE_CALL_DESK_BUTTON,
            self::TYPE_CODE_DIRECTION_BUTTON,
            self::TYPE_CODE_VIDEO_WATCHED,
        ];
    }

    /**
     * @return array
     */
    public static function getChartEventTypesWithTranslation()
    {
        $allowedEvents = self::getChartEventTypes();

        return array_intersect_key(self::EVENT_TYPES, array_flip($allowedEvents));
    }

    /**
     * @return array
     */
    public static function getAllChartEventTypesWithTranslation()
    {
        $result = self::getChartEventTypesWithTranslation();

        $result[self::TYPE_CODE_ADS]     = 'report.type.ads';
        $result[self::TYPE_CODE_KEYWORD] = 'report.type.keywords';

        return $result;
    }

    /**
     * @return array
     */
    public static function getChartHints()
    {
        return [
            self::TYPE_CODE_IMPRESSION       => 'interaction_report.hint.impression',
            self::TYPE_CODE_VIEW             => 'interaction_report.hint.view',
            self::TYPE_CODE_CALL_MOB_BUTTON  => 'interaction_report.hint.call_mob',
            self::TYPE_CODE_CALL_DESK_BUTTON => 'interaction_report.hint.call_desk',
            self::TYPE_CODE_DIRECTION_BUTTON => 'interaction_report.hint.direction',
            self::TYPE_CODE_VIDEO_WATCHED    => 'interaction_report.hint.video_watched',
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
