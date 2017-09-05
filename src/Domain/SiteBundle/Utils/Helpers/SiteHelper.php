<?php

namespace Domain\SiteBundle\Utils\Helpers;

use Doctrine\ORM\Query;
use Domain\BusinessBundle\Entity\BusinessProfile;

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
     *
     * @return mixed
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

        if (!curl_errno($handle)) {
            $info = curl_getinfo($handle);
        }

        curl_close($handle);

        return $info;
    }

    /**
     * @param Query     $query
     * @param string    $locale
     *
     * @return Query
     */
    public static function setLocaleQueryHint($query, $locale)
    {
        $query->setHint(
            \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,
            'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );

        // Force the locale
        $query->setHint(
            \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE,
            $locale
        );

        return $query;
    }
}
