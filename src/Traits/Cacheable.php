<?php

namespace Isswp101\Persimmon\Traits;

use Isswp101\Persimmon\Cache\RuntimeCache;

trait Cacheable
{
    /**
     * Runtime cache instance.
     *
     * @var RuntimeCache[]
     */
    private static $cache = [];

    /**
     * Return model cache instance.
     *
     * @return RuntimeCache
     */
    final protected static function cache(): RuntimeCache
    {
        if (!array_key_exists(static::class, self::$cache)) {
            self::$cache[static::class] = new RuntimeCache();
        }
        return self::$cache[static::class];
    }
}
