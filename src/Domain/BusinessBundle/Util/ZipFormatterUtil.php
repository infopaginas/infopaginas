<?php

namespace Domain\BusinessBundle\Util;

class ZipFormatterUtil
{
    const ZIP_CODE_SEPARATOR_POSITION = 5;
    const ZIP_CODE_SEPARATOR = '-';

    /**
     * @param string $zipCode
     *
     * @return string
     */
    public static function getFormattedZip($zipCode)
    {
        $zipCode = trim($zipCode);

        if (preg_match('/^\d{9}$/', $zipCode)) {
            $zipCode = substr_replace($zipCode, self::ZIP_CODE_SEPARATOR, self::ZIP_CODE_SEPARATOR_POSITION, 0);
        }

        return $zipCode;
    }
}
