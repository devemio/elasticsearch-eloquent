<?php

namespace Isswp101\Persimmon\DI;

final class DI
{
    const SINGLETON = true;

    private static $containers = [];
    private static $singletons = [];

    public static function bind(string $key, $abstract, bool $singleton = false): void
    {
        DI::$singletons[$key] = $singleton;
        DI::$containers[$key] = is_callable($abstract) ? $abstract : function () use ($abstract) {
            return new $abstract;
        };
    }

    public static function make(string $key)
    {
        $container = DI::$containers[$key] ?? null;
        if (is_callable($container)) {
            $container = $container();
            if (DI::$singletons[$key]) {
                DI::$containers[$key] = $container;
            }
        }
        return $container;
    }
}