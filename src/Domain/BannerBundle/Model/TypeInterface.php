<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 5/31/16
 * Time: 10:44 AM
 */

namespace Domain\BannerBundle\Model;

/**
 * Interface TypeInterface
 * @package Domain\TypeBundle\Model
 */
interface TypeInterface
{
    const CODE_HOME                 = 1;
    const CODE_PORTAL               = 2;
    const CODE_PORTAL_LEFT          = 10;
    const CODE_PORTAL_RIGHT         = 11;

    const CODE_PORTAL_LEFT_MOBILE   = 12;
    const CODE_PORTAL_RIGHT_MOBILE  = 13;

    const CODE_PORTAL_LEADERBOARD   = 3;
    const CODE_SERP_BANNER          = 4;
    const CODE_SERP_BOXED           = 5;
    const CODE_SERP_FEATUREAD       = 6;
    const CODE_SERP_MOBILE_TOP      = 7;

    const SIZE_120_420 = '120x420';
    const SIZE_300_250 = '300x250';
    const SIZE_250_250 = '250x250';
    const SIZE_728_90  = '728x90';

    const MEDIA_FORMAT_HOME         = 'home';
    const MEDIA_FORMAT_PORTAL       = 'portal';
    const MEDIA_FORMAT_SERP         = 'serp';
    const MEDIA_FORMAT_SERP_BOXED   = 'serp_boxed';

    /**
     * @return integer
     */
    public function getCode();

    /**
     * @param int $code
     * @return TypeInterface
     */
    public function setCode(int $code);

    /**
     * @return array - Media sizes
     */
    public static function getCodeSizes() : array;

    /**
     * @return array - Exists media format names
     */
    public static function getMediaFormats() : array;

    /**
     * @return string
     */
    public function getSize() : string;

    /**
     * @return string
     */
    public function getMediaFormat() : string;
}
