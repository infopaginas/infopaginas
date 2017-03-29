<?php

namespace Domain\ReportBundle\Model;

class BusinessOverviewModel implements ReportInterface
{
    const TYPE_CODE_IMPRESSION = 'impressions';
    const TYPE_CODE_VIEW       = 'views';

    const TYPE_CODE_DIRECTION_BUTTON       = 'directionButton';
    const TYPE_CODE_MAP_SHOW_BUTTON        = 'mapShowButton';
    const TYPE_CODE_MAP_MARKER_BUTTON      = 'mapMarkerButton';
    const TYPE_CODE_WEB_BUTTON             = 'webButton';
    const TYPE_CODE_CALL_MOB_BUTTON        = 'callMobButton';
    const TYPE_CODE_CALL_DESK_BUTTON       = 'callDeskButton';
    const TYPE_CODE_ADD_COMPARE_BUTTON     = 'addCompareButton';
    const TYPE_CODE_REMOVE_COMPARE_BUTTON  = 'removeCompareButton';

    const TYPE_CODE_FACEBOOK_SHARE  = 'facebookShare';
    const TYPE_CODE_TWITTER_SHARE   = 'twitterShare';

    const TYPE_CODE_FACEBOOK_VISIT     = 'facebookVisit';
    const TYPE_CODE_TWITTER_VISIT      = 'twitterVisit';
    const TYPE_CODE_GOOGLE_VISIT       = 'googleVisit';
    const TYPE_CODE_YOUTUBE_VISIT      = 'youtubeVisit';
    const TYPE_CODE_INSTAGRAM_VISIT    = 'instagramVisit';
    const TYPE_CODE_TRIP_ADVISOR_VISIT = 'tripAdvisorVisit';

    const TYPE_CODE_VIDEO_WATCHED  = 'videoWatched';
    const TYPE_CODE_REVIEW_CLICK   = 'reviewClick';
    const TYPE_CODE_EMAIL_CLICK    = 'emailClick';

    const EVENT_TYPES = [
        self::TYPE_CODE_VIEW                  => 'interaction_report.event.view',
        self::TYPE_CODE_IMPRESSION            => 'interaction_report.event.impression',
        self::TYPE_CODE_DIRECTION_BUTTON      => 'interaction_report.button.direction',
        self::TYPE_CODE_MAP_SHOW_BUTTON       => 'interaction_report.button.show_map',
        self::TYPE_CODE_MAP_MARKER_BUTTON     => 'interaction_report.button.marker_map',
        self::TYPE_CODE_WEB_BUTTON            => 'interaction_report.button.web',
        self::TYPE_CODE_CALL_MOB_BUTTON       => 'interaction_report.button.call_mob',
        self::TYPE_CODE_CALL_DESK_BUTTON      => 'interaction_report.button.call_desk',
        self::TYPE_CODE_ADD_COMPARE_BUTTON    => 'interaction_report.button.add_compare',
        self::TYPE_CODE_REMOVE_COMPARE_BUTTON => 'interaction_report.button.remove_compare',
        self::TYPE_CODE_FACEBOOK_SHARE        => 'interaction_report.share.facebook',
        self::TYPE_CODE_TWITTER_SHARE         => 'interaction_report.share.twitter',
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

    public static function getExportFormats()
    {
        return [
            self::CODE_PDF_BUSINESS_OVERVIEW_REPORT   => self::FORMAT_PDF,
            self::CODE_EXCEL_BUSINESS_OVERVIEW_REPORT => self::FORMAT_EXCEL,
        ];
    }

    public static function getTypes()
    {
        return [
            self::TYPE_CODE_IMPRESSION,
            self::TYPE_CODE_VIEW,
            self::TYPE_CODE_DIRECTION_BUTTON,
            self::TYPE_CODE_MAP_SHOW_BUTTON,
            self::TYPE_CODE_MAP_MARKER_BUTTON,
            self::TYPE_CODE_WEB_BUTTON,
            self::TYPE_CODE_CALL_MOB_BUTTON,
            self::TYPE_CODE_CALL_DESK_BUTTON,
            self::TYPE_CODE_ADD_COMPARE_BUTTON,
            self::TYPE_CODE_REMOVE_COMPARE_BUTTON,
            self::TYPE_CODE_FACEBOOK_SHARE,
            self::TYPE_CODE_TWITTER_SHARE,
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
}
