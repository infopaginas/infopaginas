<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 5/30/16
 * Time: 4:31 PM
 */

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
}
