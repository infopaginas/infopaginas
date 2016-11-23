<?php

namespace Domain\BusinessBundle\Util;

use Gedmo\Sluggable\Util\Urlizer;

class SlugUtil
{
    public static function convertSlug($slug)
    {
        $customSlug = Urlizer::transliterate($slug);

        //todo

        if ($customSlug != $slug) {
            return $customSlug;
        }

        return false;
    }
}
