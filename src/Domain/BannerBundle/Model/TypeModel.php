<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 5/31/16
 * Time: 11:58 AM
 */

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
            self::CODE_PORTAL_RIGHT         => self::MEDIA_FORMAT_PORTAL,
            self::CODE_SEARCH_PAGE_BOTTOM   => self::MEDIA_FORMAT_PORTAL,
            self::CODE_SEARCH_PAGE_TOP      => self::MEDIA_FORMAT_PORTAL,
            self::CODE_HOME_VERTICAL        => self::MEDIA_FORMAT_HOME,
            self::CODE_STATIC_BOTTOM        => self::MEDIA_FORMAT_PORTAL,
        ];
    }

    /**
     * @return array
     */
    public static function getCodeSizes() : array
    {
        return [
            self::CODE_PORTAL_RIGHT         => self::SIZE_300_250,
            self::CODE_SEARCH_PAGE_BOTTOM   => self::SIZE_AUTO_SEARCH,
            self::CODE_SEARCH_PAGE_TOP      => self::SIZE_AUTO_SEARCH,
            self::CODE_HOME_VERTICAL        => self::SIZE_AUTO_STATIC,
            self::CODE_STATIC_BOTTOM        => self::SIZE_AUTO_STATIC,
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
     * @return mixed
     */
    public function getMediaFormat() : string
    {
        $mediaFormats = self::getMediaFormats();

        return $mediaFormats[$this->getCode()];
    }
}
