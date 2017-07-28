<?php

namespace Domain\BannerBundle\Model;

use Gedmo\Exception\InvalidArgumentException;

class TypeModel implements TypeInterface
{
    /**
     * @var integer
     */
    protected $code;

    /**
     * @return array
     */
    public static function getMediaFormats() : array
    {
        return [
            self::CODE_LANDING_PAGE_RIGHT   => self::MEDIA_FORMAT_HOME,
            self::CODE_BUSINESS_PAGE_RIGHT  => self::MEDIA_FORMAT_BUSINESS,
            self::CODE_ARTICLE_PAGE_RIGHT   => self::MEDIA_FORMAT_ARTICLE,
            self::CODE_VIDEO_PAGE_RIGHT     => self::MEDIA_FORMAT_VIDEO,
            self::CODE_HOME_VERTICAL        => self::MEDIA_FORMAT_HOME,
            self::CODE_SEARCH_PAGE_TOP      => self::MEDIA_FORMAT_SEARCH,
            self::CODE_SEARCH_PAGE_BOTTOM   => self::MEDIA_FORMAT_SEARCH,
            self::CODE_COMPARE_PAGE_TOP     => self::MEDIA_FORMAT_COMPARE,
            self::CODE_COMPARE_PAGE_BOTTOM  => self::MEDIA_FORMAT_COMPARE,
            self::CODE_BUSINESS_PAGE_BOTTOM => self::MEDIA_FORMAT_BUSINESS,
            self::CODE_ARTICLE_PAGE_BOTTOM  => self::MEDIA_FORMAT_ARTICLE,
            self::CODE_VIDEO_PAGE_BOTTOM    => self::MEDIA_FORMAT_VIDEO,
            self::CODE_PORTAL_RIGHT         => self::MEDIA_FORMAT_STATIC,
            self::CODE_STATIC_BOTTOM        => self::MEDIA_FORMAT_STATIC,
            self::CODE_SEARCH_FLOAT_BOTTOM  => self::MEDIA_FORMAT_FLOAT,
        ];
    }

    /**
     * @return array
     */
    public static function getCodeSizes() : array
    {
        return [
            self::CODE_LANDING_PAGE_RIGHT   => self::SIZE_300_250,
            self::CODE_BUSINESS_PAGE_RIGHT  => self::SIZE_300_250,
            self::CODE_ARTICLE_PAGE_RIGHT   => self::SIZE_300_250,
            self::CODE_VIDEO_PAGE_RIGHT     => self::SIZE_300_250,
            self::CODE_HOME_VERTICAL        => self::SIZE_AUTO_STATIC,
            self::CODE_SEARCH_PAGE_TOP      => self::SIZE_AUTO_STATIC,
            self::CODE_SEARCH_PAGE_BOTTOM   => self::SIZE_AUTO_STATIC,
            self::CODE_COMPARE_PAGE_TOP     => self::SIZE_300_250,
            self::CODE_COMPARE_PAGE_BOTTOM  => self::SIZE_300_250,
            self::CODE_BUSINESS_PAGE_BOTTOM => self::SIZE_AUTO_STATIC,
            self::CODE_ARTICLE_PAGE_BOTTOM  => self::SIZE_AUTO_STATIC,
            self::CODE_VIDEO_PAGE_BOTTOM    => self::SIZE_AUTO_STATIC,
            self::CODE_PORTAL_RIGHT         => self::SIZE_300_250,
            self::CODE_STATIC_BOTTOM        => self::SIZE_AUTO_STATIC,
            self::CODE_SEARCH_FLOAT_BOTTOM  => self::SIZE_320_50,
        ];
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

    /**
     * @return string
     */
    public function getSize() : string
    {
        $codeSizes = self::getCodeSizes();

        return $codeSizes[$this->getCode()];
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param int $code
     * @return mixed
     */
    public function setCode(int $code)
    {
        if (!array_key_exists($code, self::getCodeSizes())) {
            throw new InvalidArgumentException('Unknown type code');
        }

        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getMediaFormat() : string
    {
        $mediaFormats = self::getMediaFormats();

        return $mediaFormats[$this->getCode()];
    }
}
