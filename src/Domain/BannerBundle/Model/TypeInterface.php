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
    const CODE_HOME_VERTICAL        = 1;
    const CODE_PORTAL_RIGHT         = 2;
    const CODE_SEARCH_PAGE_BOTTOM   = 3;
    const CODE_SEARCH_PAGE_TOP      = 4;
    const CODE_STATIC_BOTTOM        = 5;

    const SIZE_300_250 = '300x250';
    const SIZE_AUTO_SEARCH = '320x50, 468x60';
    const SIZE_AUTO_STATIC = '320x50, 728x90';

    const MEDIA_FORMAT_HOME         = 'home';
    const MEDIA_FORMAT_PORTAL       = 'portal';

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
