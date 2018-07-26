<?php

namespace Oxa\ConfigBundle\Model;

/**
 * Interface ConfigInterface
 * @package Oxa\ConfigBundle\Model
 */
interface ConfigInterface
{
    const DEFAULT_TITLE                     = 'DEFAULT_TITLE';
    const DEFAULT_META_DESCRIPTION          = 'META_DESCRIPTION';
    const DEFAULT_META_KEYWORDS             = 'META_KEYWORDS';
    const FOOTER_CONTENT                    = 'FOOTER_CONTENT';
    const DEFAULT_EMAIL_ADDRESS             = 'DEFAULT_EMAIL_ADDRESS';
    const MAIL_REGISTRATION_TEMPLATE        = 'MAIL_REGISTRATION_TEMPLATE';
    const MAIL_NEW_MERCHANT_TEMPLATE        = 'MAIL_NEW_MERCHANT_TEMPLATE';
    const MAIL_RESET_PASSWORD_TEMPLATE      = 'MAIL_RESET_PASSWORD_TEMPLATE';
    const MAIL_TEMPLATE_TO_USER             = 'MAIL_TEMPLATE_TO_USER';
    const MAIL_CHANGE_WAS_REJECTED          = 'MAIL_CHANGE_WAS_REJECTED';
    const SOCIAL_FACEBOOK_PROFILE           = 'SOCIAL_FACEBOOK_PROFILE';
    const SOCIAL_TWITTER_PROFILE            = 'SOCIAL_TWITTER_PROFILE';
    const SOCIAL_GOOGLE_PROFILE             = 'SOCIAL_GOOGLE_PROFILE';
    const SOCIAL_LINKEDIN_PROFILE           = 'SOCIAL_LINKEDIN_PROFILE';
    const SOCIAL_INSTAGRAM_PROFILE          = 'SOCIAL_INSTAGRAM_PROFILE';
    const GOOGLE_API_KEY                    = 'GOOGLE_API_KEY';
    const DEFAULT_MAP_COORDINATE_LATITUDE   = 'DEFAULT_MAP_COORDINATE_LATITUDE';
    const DEFAULT_MAP_COORDINATE_LONGITUDE  = 'DEFAULT_MAP_COORDINATE_LONGITUDE';
    const DEFAULT_RESULTS_PAGE_SIZE         = 'DEFAULT_RESULTS_PAGE_SIZE';
    const DEFAULT_SEARCH_CITY               = 'DEFAULT_SEARCH_CITY';
    const YOUTUBE_ACCESS_TOKEN              = 'YOUTUBE_ACCESS_TOKEN';
    const YOUTUBE_ERROR_EMAIL_TEMPLATE      = 'YOUTUBE_ERROR_EMAIL_TEMPLATE';
    const ARTICLE_API_ERROR_EMAIL_TEMPLATE  = 'ARTICLE_API_ERROR_EMAIL_TEMPLATE';
    const SEARCH_ADS_ALLOWED                = 'SEARCH_ADS_ALLOWED';
    const SEARCH_ADS_MAX_PAGE               = 'SEARCH_ADS_MAX_PAGE';
    const SEARCH_ADS_PER_PAGE               = 'SEARCH_ADS_PER_PAGE';
    const MAIL_REPORT_EXPORT_PROCESSED      = 'MAIL_REPORT_EXPORT_PROCESSED';
    const FEEDBACK_EMAIL_ADDRESS            = 'FEEDBACK_EMAIL_ADDRESS';
    const FEEDBACK_EMAIL_SUBJECT            = 'FEEDBACK_EMAIL_SUBJECT';
    const EMERGENCY_SITUATION_ON            = 'EMERGENCY_SITUATION_ON';
    const EMERGENCY_CATALOG_ORDER_BY_ALPHABET = 'EMERGENCY_CATALOG_ORDER_BY_ALPHABET';
    const GOOGLE_OPTIMIZATION_CONTAINER_ID  = 'GOOGLE_CONTAINER_ID';
    const SUGGEST_CATEGORY_MINIMUM_MATCH    = 'SUGGEST_CATEGORY_MINIMUM_MATCH';
    const SUGGEST_LOCALITY_MINIMUM_MATCH    = 'SUGGEST_LOCALITY_MINIMUM_MATCH';
    const STATUS_WAS_CHANGED_EMAIL_TEMPLATE = 'STATUS_WAS_CHANGED_EMAIL_TEMPLATE';
    const UPDATE_PROFILE_REQUEST_EMAIL_TEMPLATE = 'MAIL_UPDATE_PROFILE_REQUEST_TEMPLATE';
    const UPDATE_PROFILE_REQUEST_EMAIL_ADDRESS  = 'UPDATE_PROFILE_REQUEST_EMAIL_ADDRESS';
    const UPDATE_PROFILE_REQUEST_EMAIL_SUBJECT  = 'UPDATE_PROFILE_REQUEST_SUBJECT';
}
