<?php

namespace Domain\SiteBundle\Utils\Helpers;

class SiteHelper
{
    const CURL_TIMEOUT = 10;

    /**
     * Data had been got from php.net mime types
     */
    public static $imageContentTypes = [
        'image/png',
        'image/jpeg',
        'image/gif',
        'image/bmp',
    ];

    public static $videoContentTypes = [
        'video/quicktime',
        'application/x-troff-msvideo',
        'video/avi',
        'video/msvideo',
        'video/x-msvideo',
        'video/mpeg',
        'video/mp4',
        'video/x-ms-wmv',
        'video/x-flv'
    ];

    /**
     * @param string $url
     */
    public static function checkUrlExistence(string $url)
    {
        $info = null;

        $handle = curl_init($url);

        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($handle, CURLOPT_HEADER, true);
        curl_setopt($handle, CURLOPT_NOBODY, true);
        curl_setopt($handle, CURLOPT_USERAGENT, true);
        curl_setopt($handle, CURLOPT_TIMEOUT, self::CURL_TIMEOUT);

        curl_exec($handle);

        if(!curl_errno($handle)) {
            $info = curl_getinfo($handle);
        }

        curl_close($handle);

        return $info;
    }

    public static function generateBusinessSubfolder($businessId)
    {
        return substr($businessId, -1) . DIRECTORY_SEPARATOR . $businessId;
    }
}
