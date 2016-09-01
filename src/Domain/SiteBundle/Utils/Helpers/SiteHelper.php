<?php

namespace Domain\SiteBundle\Utils\Helpers;

class SiteHelper
{
    const CURL_TIMEOUT = 10;

    /**
     * @param string $url
     */
    public static function checkUrlExistence(string $url)
    {
        $handle = curl_init($url);

        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($handle, CURLOPT_HEADER, true);
        curl_setopt($handle, CURLOPT_NOBODY, true);
        curl_setopt($handle, CURLOPT_USERAGENT, true);
        curl_setopt($handle, CURLOPT_TIMEOUT, self::CURL_TIMEOUT);

        $headers = curl_exec($handle);

        curl_close($handle);

        return $headers;
    }
}
