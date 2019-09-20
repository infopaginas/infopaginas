<?php

namespace Domain\BannerBundle\Model;

class TypeModel implements TypeInterface
{
    /**
     * @return array
     */
    public static function getCodeSizes() : array
    {
        return [
            self::CODE_LANDING_PAGE_RIGHT        => self::SIZE_300_250,
            self::CODE_BUSINESS_PAGE_RIGHT       => self::SIZE_300_250,
            self::CODE_ARTICLE_PAGE_RIGHT        => self::SIZE_300_250,
            self::CODE_VIDEO_PAGE_RIGHT          => self::SIZE_300_250,
            self::CODE_PORTAL_RIGHT              => self::SIZE_300_250,
            self::CODE_HOME_VERTICAL             => self::SIZE_AUTO_STATIC,
            self::CODE_SEARCH_PAGE_TOP           => self::SIZE_AUTO_STATIC,
            self::CODE_SEARCH_PAGE_BOTTOM        => self::SIZE_AUTO_STATIC,
            self::CODE_BUSINESS_PAGE_BOTTOM      => self::SIZE_AUTO_STATIC,
            self::CODE_ARTICLE_PAGE_BOTTOM       => self::SIZE_AUTO_STATIC,
            self::CODE_VIDEO_PAGE_BOTTOM         => self::SIZE_AUTO_STATIC,
            self::CODE_STATIC_BOTTOM             => self::SIZE_AUTO_STATIC,
            self::CODE_COMPARE_PAGE_TOP          => self::SIZE_320_50,
            self::CODE_COMPARE_PAGE_BOTTOM       => self::SIZE_320_50,
            self::CODE_SEARCH_FLOAT_BOTTOM       => self::SIZE_320_50,
            self::CODE_LANDING_PAGE_RIGHT_LARGE  => self::SIZE_AUTO_SIDEBLOCK,
            self::CODE_BUSINESS_PAGE_RIGHT_LARGE => self::SIZE_AUTO_SIDEBLOCK,
            self::CODE_ARTICLE_PAGE_RIGHT_LARGE  => self::SIZE_AUTO_SIDEBLOCK,
            self::CODE_VIDEO_PAGE_RIGHT_LARGE    => self::SIZE_AUTO_SIDEBLOCK,
            self::CODE_PORTAL_RIGHT_LARGE        => self::SIZE_AUTO_SIDEBLOCK,
        ];
    }

    /**
     * @return array
     */
    public static function getCodeSizeData() : array
    {
        return [
            self::CODE_LANDING_PAGE_RIGHT   => [
                self::SIZE_DATA_300_250,
            ],
            self::CODE_BUSINESS_PAGE_RIGHT  => [
                self::SIZE_DATA_300_250,
            ],
            self::CODE_ARTICLE_PAGE_RIGHT   => [
                self::SIZE_DATA_300_250,
            ],
            self::CODE_VIDEO_PAGE_RIGHT     => [
                self::SIZE_DATA_300_250,
            ],
            self::CODE_PORTAL_RIGHT         => [
                self::SIZE_DATA_300_250,
            ],
            self::CODE_HOME_VERTICAL        => [
                self::SIZE_DATA_320_50,
                self::SIZE_DATA_728_90,
            ],
            self::CODE_SEARCH_PAGE_TOP      => [
                self::SIZE_DATA_320_50,
                self::SIZE_DATA_728_90,
            ],
            self::CODE_SEARCH_PAGE_BOTTOM   => [
                self::SIZE_DATA_320_50,
                self::SIZE_DATA_728_90,
            ],
            self::CODE_BUSINESS_PAGE_BOTTOM => [
                self::SIZE_DATA_320_50,
                self::SIZE_DATA_728_90,
            ],
            self::CODE_ARTICLE_PAGE_BOTTOM  => [
                self::SIZE_DATA_320_50,
                self::SIZE_DATA_728_90,
            ],
            self::CODE_VIDEO_PAGE_BOTTOM    => [
                self::SIZE_DATA_320_50,
                self::SIZE_DATA_728_90,
            ],
            self::CODE_STATIC_BOTTOM        => [
                self::SIZE_DATA_320_50,
                self::SIZE_DATA_728_90,
            ],
            self::CODE_COMPARE_PAGE_TOP     => [
                self::SIZE_DATA_320_50,
            ],
            self::CODE_COMPARE_PAGE_BOTTOM  => [
                self::SIZE_DATA_320_50,
            ],
            self::CODE_SEARCH_FLOAT_BOTTOM  => [
                self::SIZE_DATA_320_50,
            ],
            self::CODE_LANDING_PAGE_RIGHT_LARGE => [
                self::SIZE_DATA_300_250,
                self::SIZE_DATA_300_600,
            ],
            self::CODE_BUSINESS_PAGE_RIGHT_LARGE => [
                self::SIZE_DATA_300_250,
                self::SIZE_DATA_300_600,
            ],
            self::CODE_ARTICLE_PAGE_RIGHT_LARGE => [
                self::SIZE_DATA_300_250,
                self::SIZE_DATA_300_600,
            ],
            self::CODE_VIDEO_PAGE_RIGHT_LARGE => [
                self::SIZE_DATA_300_250,
                self::SIZE_DATA_300_600,
            ],
            self::CODE_PORTAL_RIGHT_LARGE => [
                self::SIZE_DATA_300_250,
                self::SIZE_DATA_300_600,
            ],
        ];
    }

    /**
     * @param string $code
     *
     * @return string
     */
    public static function getBannerResizableTypeByCode($code)
    {
        if (in_array($code, self::getBannerResizable())) {
            $resizableType = self::BANNER_TYPE_RESIZABLE;
        } elseif (in_array($code, TypeModel::getBannerResizableInBlock())) {
            $resizableType = self::BANNER_TYPE_RESIZABLE_BLOCK;
        } elseif (in_array($code, self::getBannerResizableSideBlock())) {
            $resizableType = self::BANNER_TYPE_RESIZABLE_SIDE_BLOCK;
        } else {
            $resizableType = self::BANNER_TYPE_DEFAULT;
        }

        return $resizableType;
    }

    /**
     * @return array
     */
    public static function getBannerTypes() : array
    {
        return [
            self::CODE_LANDING_PAGE_RIGHT,
            self::CODE_BUSINESS_PAGE_RIGHT,
            self::CODE_ARTICLE_PAGE_RIGHT,
            self::CODE_VIDEO_PAGE_RIGHT,
            self::CODE_HOME_VERTICAL,
            self::CODE_SEARCH_PAGE_TOP,
            self::CODE_SEARCH_PAGE_BOTTOM,
            self::CODE_COMPARE_PAGE_TOP,
            self::CODE_COMPARE_PAGE_BOTTOM,
            self::CODE_BUSINESS_PAGE_BOTTOM,
            self::CODE_ARTICLE_PAGE_BOTTOM,
            self::CODE_VIDEO_PAGE_BOTTOM,
            self::CODE_PORTAL_RIGHT,
            self::CODE_STATIC_BOTTOM,
            self::CODE_SEARCH_FLOAT_BOTTOM,
            self::CODE_LANDING_PAGE_RIGHT_LARGE,
            self::CODE_BUSINESS_PAGE_RIGHT_LARGE,
            self::CODE_ARTICLE_PAGE_RIGHT_LARGE,
            self::CODE_VIDEO_PAGE_RIGHT_LARGE,
            self::CODE_PORTAL_RIGHT_LARGE,
        ];
    }

    /**
     * @return array
     */
    public static function getBannerResizable() : array
    {
        return [
            self::CODE_HOME_VERTICAL,
            self::CODE_BUSINESS_PAGE_BOTTOM,
            self::CODE_ARTICLE_PAGE_BOTTOM,
            self::CODE_VIDEO_PAGE_BOTTOM,
            self::CODE_STATIC_BOTTOM,
        ];
    }

    /**
     * @return array
     */
    public static function getBannerResizableInBlock() : array
    {
        return [
            self::CODE_SEARCH_PAGE_TOP,
            self::CODE_SEARCH_PAGE_BOTTOM,
        ];
    }

    public static function getBannerResizableSideBlock() : array
    {
        return [
            self::CODE_LANDING_PAGE_RIGHT_LARGE,
            self::CODE_BUSINESS_PAGE_RIGHT_LARGE,
            self::CODE_ARTICLE_PAGE_RIGHT_LARGE,
            self::CODE_VIDEO_PAGE_RIGHT_LARGE,
            self::CODE_PORTAL_RIGHT_LARGE,
        ];
    }

    /**
     * @param int $code
     *
     * @return string
     */
    public static function getSizeByCode($code) : string
    {
        $codeSizes = self::getCodeSizes();

        return $codeSizes[$code];
    }

    public static function getDefaultBannerSettings()
    {
        return [
            [
                'code'      => TypeInterface::CODE_LANDING_PAGE_RIGHT,
                'name'      => 'Landing Page 300x250',
                'placement' => 'Landing Page',
                'comment'   => 'Ad block in the right column of Landing Page',
                'htmlId'    => 'div-gpt-ad-1487767555459-0',
                'slotId'    => '/101238367/landing_page_right',
            ],
            [
                'code'      => TypeInterface::CODE_BUSINESS_PAGE_RIGHT,
                'name'      => 'Business Profile Page 300x250',
                'placement' => 'Business Profile Pages',
                'comment'   => 'Ad block in the right column of Business Profile Pages',
                'htmlId'    => 'div-gpt-ad-1487767783710-0',
                'slotId'    => '/101238367/business_page_right',
            ],
            [
                'code'      => TypeInterface::CODE_ARTICLE_PAGE_RIGHT,
                'name'      => 'Articles 300x250',
                'placement' => 'Article List and Article Page',
                'comment'   => 'Ad block in the right column of Article List and Article Page',
                'htmlId'    => 'div-gpt-ad-1487767912115-0',
                'slotId'    => '/101238367/article_page_right',
            ],
            [
                'code'      => TypeInterface::CODE_VIDEO_PAGE_RIGHT,
                'name'      => 'Videos 300x250',
                'placement' => 'Video List',
                'comment'   => 'Ad block in the right column of Video List',
                'htmlId'    => 'div-gpt-ad-1487863376890-0',
                'slotId'    => '/101238367/video_page_right',
            ],
            [
                'code'      => TypeInterface::CODE_PORTAL_RIGHT,
                'name'      => 'Static 300x250',
                'placement' => 'Static pages (advertise, contact us, terms, privacy)',
                'comment'   => 'Ad block in the right column of Static pages',
                'htmlId'    => 'div-gpt-ad-1487768446005-0',
                'slotId'    => '/101238367/static_page_right',
            ],
            [
                'code'      => TypeInterface::CODE_HOME_VERTICAL,
                'name'      => 'Landing Page 728x90 and 320x50',
                'placement' => 'Landing Page',
                'comment'   => 'Vertical ad block under search (should be set up for both sizes)',
                'htmlId'    => 'div-gpt-ad-1487770389634-0',
                'slotId'    => '/101238367/landing_vertical_auto',
            ],
            [
                'code'      => TypeInterface::CODE_SEARCH_PAGE_TOP,
                'name'      => 'Search Results Top 728x90 and 320x50',
                'placement' => 'Search Results Page and Catalog',
                'comment'   => 'Vertical ad block on top of search results (should be set up for both sizes)',
                'htmlId'    => 'div-gpt-ad-1487771173720-0',
                'slotId'    => '/101238367/search_auto_top',
            ],
            [
                'code'      => TypeInterface::CODE_SEARCH_PAGE_BOTTOM,
                'name'      => 'Search Results Bottom 728x90 and 320x50',
                'placement' => 'Search Results Page and Catalog',
                'comment'   => 'Vertical ad block at the bottom of search results (should be set up for both sizes)',
                'htmlId'    => 'div-gpt-ad-1487773227312-0',
                'slotId'    => '/101238367/search_auto_bottom',
            ],
            [
                'code'      => TypeInterface::CODE_COMPARE_PAGE_TOP,
                'name'      => 'Compare Page Top 320x50',
                'placement' => 'Compare page',
                'comment'   => 'Vertical ad block on top of compare results',
                'htmlId'    => 'div-gpt-ad-1487774027676-0',
                'slotId'    => '/101238367/comapre_top',
            ],
            [
                'code'      => TypeInterface::CODE_COMPARE_PAGE_BOTTOM,
                'name'      => 'Compare Page Bottom 320x50',
                'placement' => 'Compare page',
                'comment'   => 'Vertical ad block at the bottom of compare results',
                'htmlId'    => 'div-gpt-ad-1487775103017-0',
                'slotId'    => '/101238367/compare_bottom',
            ],
            [
                'code'      => TypeInterface::CODE_BUSINESS_PAGE_BOTTOM,
                'name'      => 'Business Profile Page 728x90 and 320x50',
                'placement' => 'Business Profile Page',
                'comment'   => 'Ad block at the bottom of Business Profile Pages',
                'htmlId'    => 'div-gpt-ad-1487775652332-0',
                'slotId'    => '/101238367/business-auto-bottom',
            ],
            [
                'code'      => TypeInterface::CODE_ARTICLE_PAGE_BOTTOM,
                'name'      => 'Articles 728x90 and 320x50',
                'placement' => 'Article List and Article Page',
                'comment'   => 'Ad block at the bottom of Article List and Article Page',
                'htmlId'    => 'div-gpt-ad-1487776175277-0',
                'slotId'    => '/101238367/article-auto-bottom',
            ],
            [
                'code'      => TypeInterface::CODE_VIDEO_PAGE_BOTTOM,
                'name'      => 'Videos 728x90 and 320x50',
                'placement' => 'Video List',
                'comment'   => 'Ad block at the bottom of Video List',
                'htmlId'    => 'div-gpt-ad-1487776671754-0',
                'slotId'    => '/101238367/video-auto-bottom',
            ],
            [
                'code'      => TypeInterface::CODE_STATIC_BOTTOM,
                'name'      => 'Static 728x90 and 320x50',
                'placement' => 'Static pages (advertise, contact us, terms, privacy)',
                'comment'   => 'Ad block at the bottom of static pages',
                'htmlId'    => 'div-gpt-ad-1487777338069-0',
                'slotId'    => '/101238367/static_page_auto_bottom',
            ],
            [
                'code'      => TypeInterface::CODE_SEARCH_FLOAT_BOTTOM,
                'name'      => 'Floating Banner at Search Page 320x50',
                'placement' => 'Search Results Page and Catalog',
                'comment'   => 'Floating ad block at the bottom of search and catalog pages',
                'htmlId'    => 'div-gpt-ad-1492095039573-0',
                'slotId'    => '/101238367/qa_search_float_bottom',
            ],
            [
                'code'      => TypeInterface::CODE_LANDING_PAGE_RIGHT_LARGE,
                'name'      => 'Landing Page 300x250 and 300x600',
                'placement' => 'Landing Page',
                'comment'   => 'Ad block in the right column of Landing Page',
                'htmlId'    => 'div-gpt-ad-1568730199302-0',
                'slotId'    => '/101238367/landing_page_right_large',
            ],
            [
                'code'      => TypeInterface::CODE_BUSINESS_PAGE_RIGHT_LARGE,
                'name'      => 'Business Profile Page 300x250 and 300x600',
                'placement' => 'Business Profile Pages',
                'comment'   => 'Ad block in the right column of Business Profile Pages',
                'htmlId'    => 'div-gpt-ad-1568730717834-0',
                'slotId'    => '/101238367/business_page_right_large',
            ],
            [
                'code'      => TypeInterface::CODE_ARTICLE_PAGE_RIGHT_LARGE,
                'name'      => 'Articles 300x250 and 300x600',
                'placement' => 'Article List and Article Page',
                'comment'   => 'Ad block in the right column of Article List and Article Page',
                'htmlId'    => 'div-gpt-ad-1568730474743-0',
                'slotId'    => '/101238367/article_page_right_large',
            ],
            [
                'code'      => TypeInterface::CODE_VIDEO_PAGE_RIGHT_LARGE,
                'name'      => 'Videos 300x250 and 300x600',
                'placement' => 'Video List',
                'comment'   => 'Ad block in the right column of Video List',
                'htmlId'    => 'div-gpt-ad-1568730350035-0',
                'slotId'    => '/101238367/video_page_right_large',
            ],
            [
                'code'      => TypeInterface::CODE_PORTAL_RIGHT_LARGE,
                'name'      => 'Static 300x250 and 300x600',
                'placement' => 'Static pages (advertise, contact us, terms, privacy)',
                'comment'   => 'Ad block in the right column of Static pages',
                'htmlId'    => 'div-gpt-ad-1568730392474-0',
                'slotId'    => '/101238367/static_page_right_large',
            ],
        ];
    }
}
