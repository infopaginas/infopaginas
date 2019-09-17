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
    const CODE_LANDING_PAGE_RIGHT_LARGE  = 16;
    const CODE_BUSINESS_PAGE_RIGHT_LARGE = 17;
    const CODE_ARTICLE_PAGE_RIGHT_LARGE  = 18;
    const CODE_VIDEO_PAGE_RIGHT_LARGE    = 19;
    const CODE_PORTAL_RIGHT_LARGE        = 20;

    const SIZE_300_250 = '300x250';
    const SIZE_AUTO_SIDEBLOCK = '300x250, 300x600';
    const SIZE_320_50  = '320x50';
    const SIZE_AUTO_STATIC = '320x50, 728x90';

    const SIZE_DATA_300_250 = [300, 250];
    const SIZE_DATA_320_50  = [320, 50];
    const SIZE_DATA_728_90  = [728, 90];
    const SIZE_DATA_300_600 = [300, 600];

    const BANNER_TYPE_DEFAULT           = 'default';
    const BANNER_TYPE_RESIZABLE         = 'resizable';
    const BANNER_TYPE_RESIZABLE_BLOCK   = 'resizableBlock';
}
