<?php

namespace Domain\BusinessBundle\Util;

use Domain\BusinessBundle\Entity\BusinessProfilePhone;

class PhoneFormatterUtil
{
    const PHONE_SEPARATOR_POSITION_1 = 3;
    const PHONE_SEPARATOR_POSITION_2 = 6;
    const PHONE_SEPARATOR = '-';

    /**
     * @param string $phone
     *
     * @return string
     */
    public static function getFormattedPhone($phone)
    {
        if (!preg_match(BusinessProfilePhone::REGEX_PHONE_PATTERN, $phone)) {
            $phone = preg_replace('/\D/', '', $phone);

            $phone = substr_replace($phone, self::PHONE_SEPARATOR, self::PHONE_SEPARATOR_POSITION_2, 0);
            $phone = substr_replace($phone, self::PHONE_SEPARATOR, self::PHONE_SEPARATOR_POSITION_1, 0);
        }

        if (!preg_match(BusinessProfilePhone::REGEX_PHONE_PATTERN, $phone)) {
            $phone = '';
        }

        return $phone;
    }
}
