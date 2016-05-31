<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 5/31/16
 * Time: 11:58 AM
 */

namespace Domain\BannerBundle\Model;


use Gedmo\Exception\InvalidArgumentException;
use Oxa\Sonata\MediaBundle\Model\OxaMediaInterface;

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
            self::CODE_HOME                 => self::MEDIA_FORMAT_HOME,
            self::CODE_PORTAL               => self::MEDIA_FORMAT_PORTAL,
            self::CODE_PORTAL_LEADERBOARD   => self::MEDIA_FORMAT_SERP,
            self::CODE_SERP_BANNER          => self::MEDIA_FORMAT_SERP,
            self::CODE_SERP_BOXED           => self::MEDIA_FORMAT_SERP_BOXED,
            self::CODE_SERP_FEATUREAD       => self::MEDIA_FORMAT_SERP,
            self::CODE_SERP_MOBILE_TOP      => self::MEDIA_FORMAT_SERP,
        ];
    }

    /**
     * @return array
     */
    public static function getCodeSizes() : array
    {
        return [
            self::CODE_HOME                 => self::SIZE_120_420,
            self::CODE_PORTAL               => self::SIZE_300_250,
            self::CODE_PORTAL_LEADERBOARD   => self::SIZE_728_90,
            self::CODE_SERP_BANNER          => self::SIZE_728_90,
            self::CODE_SERP_BOXED           => self::SIZE_250_250,
            self::CODE_SERP_FEATUREAD       => self::SIZE_728_90,
            self::CODE_SERP_MOBILE_TOP      => self::SIZE_728_90,
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
