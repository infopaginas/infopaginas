<?php

namespace Domain\BusinessBundle\Util;

use Gedmo\Sluggable\Util\Urlizer;

class SlugUtil
{
    /**
     * @param string $slug
     *
     * @return string
     */
    public static function convertSlug($slug)
    {
        $customSlug = self::convertCustomSlug($slug);

        if ($customSlug) {
            return $customSlug;
        }

        return $slug;
    }

    /**
     * @param string $slug
     *
     * @return string|null
     */
    public static function convertCustomSlug($slug)
    {
        $customSlug = Urlizer::transliterate($slug);

        if ($customSlug != $slug) {
            return $customSlug;
        }

        return null;
    }
}
