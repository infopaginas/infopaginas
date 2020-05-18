<?php

namespace Domain\SearchBundle\Util;

use Doctrine\Common\Cache\CacheProvider;

class CacheUtil
{
    public const PREFIX_HOMEPAGE_SHORTCUT = 'homepage_shortcuts_';
    public const PREFIX_AUTOCOMPLETE = 'autocomplete_';

    public const ID_CONFIGS = 'configs';

    public const SECONDS_IN_MONTH = 2592000;
    public const SECONDS_IN_DAY = 86400;
    public const AUTOCOMPLETE_CACHE_LIFETIME = 300;
    public const HOMEPAGE_SHORTCUT_CACHE_LIFETIME = self::SECONDS_IN_MONTH;

    /**
     * @param CacheProvider $cache
     * @param string $prefix
     * @param int $lifeTime
     */
    public static function invalidateCacheByPrefix(CacheProvider $cache, string $prefix, $lifeTime = 0): void
    {
        $keyIncrement = $cache->fetch($prefix);
        if ($keyIncrement) {
            $cache->save($prefix, $keyIncrement + 1, $lifeTime);
        }
    }
}