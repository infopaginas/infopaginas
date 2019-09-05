<?php

namespace Domain\BusinessBundle\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ArrayType;
use Domain\BusinessBundle\VO\Url;

class UrlType extends ArrayType
{
    const URL = 'urlType';

    const REL_NO_FOLLOW     = 'REL_NO_FOLLOW';
    const REL_NO_OPENER     = 'REL_NO_OPENER';
    const REL_NO_REFERRER   = 'REL_NO_REFERRER';
    const URL_NAME          = 'URL_NAME';

    public function getName()
    {
        return self::URL;
    }

    /**
     * @param Url $value
     * @param AbstractPlatform $platform
     *
     * @return mixed|string
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        $urlValue = $value instanceof Url ? $value->toArray() : null;

        return parent::convertToDatabaseValue($urlValue, $platform);
    }


    /**
     * @param $value
     * @param AbstractPlatform $platform
     * @return Url|null
     * @throws \Doctrine\DBAL\Types\ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $array = parent::convertToPHPValue($value, $platform);

        $url = new Url();

        $url->setUrl($this->getValueFromArray(self::URL_NAME, $array, ''));
        $url->setRelNoFollow($this->getValueFromArray(self::REL_NO_FOLLOW, $array));
        $url->setRelNoOpener($this->getValueFromArray(self::REL_NO_OPENER, $array));
        $url->setRelNoReferrer($this->getValueFromArray(self::REL_NO_REFERRER, $array));

        return $url;
    }

    /**
     * @param string $key
     * @param array $array
     * @param mixed $default
     *
     * @return bool
     */
    protected function getValueFromArray($key, $array, $default = true)
    {
        if ($array && array_key_exists($key, $array)) {
            return $array[$key];
        }

        return $default;
    }
}