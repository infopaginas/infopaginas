<?php

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
    const CODE_LANDING_PAGE_RIGHT   = 6;
    const CODE_BUSINESS_PAGE_RIGHT  = 7;
    const CODE_ARTICLE_PAGE_RIGHT   = 8;
    const CODE_VIDEO_PAGE_RIGHT     = 9;
    const CODE_COMPARE_PAGE_TOP     = 10;
    const CODE_COMPARE_PAGE_BOTTOM  = 11;
    const CODE_BUSINESS_PAGE_BOTTOM = 12;
    const CODE_ARTICLE_PAGE_BOTTOM  = 13;
    const CODE_VIDEO_PAGE_BOTTOM    = 14;
    const CODE_SEARCH_FLOAT_BOTTOM  = 15;

    const SIZE_300_250 = '300x250';
    const SIZE_320_50  = '320x50';
    const SIZE_AUTO_SEARCH = '320x50, 468x60';
    const SIZE_AUTO_STATIC = '320x50, 728x90';

    const MEDIA_FORMAT_HOME         = 'home';
    const MEDIA_FORMAT_BUSINESS     = 'business';
    const MEDIA_FORMAT_ARTICLE      = 'article';
    const MEDIA_FORMAT_VIDEO        = 'video';
    const MEDIA_FORMAT_SEARCH       = 'search';
    const MEDIA_FORMAT_COMPARE      = 'compare';
    const MEDIA_FORMAT_STATIC       = 'static';
    const MEDIA_FORMAT_PORTAL       = 'portal';
    const MEDIA_FORMAT_FLOAT        = 'float';

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
