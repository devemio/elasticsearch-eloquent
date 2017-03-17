<?php

namespace Isswp101\Persimmon\DI;

use Elasticsearch\ClientBuilder;
use Isswp101\Persimmon\Exceptions\BindingResolutionException;
use Isswp101\Persimmon\Repository\ElasticsearchRepository;
use Isswp101\Persimmon\Repository\RuntimeCacheRepository;

final class DI
{
    const ELASTICSEARCH = 'elasticsearch';

    private static $containers = [];
    private static $singletons = [];
    private static $initialized = false;

    private static function init(): void
    {
        DI::bind(DI::ELASTICSEARCH, function () {
            $client = ClientBuilder::create()->build();
            return new Container(new ElasticsearchRepository($client), new RuntimeCacheRepository());
        });
    }

    public static function bind(string $key, $abstract, bool $singleton = true): void
    {
        DI::$singletons[$key] = $singleton;
        DI::$containers[$key] = is_callable($abstract) ? $abstract : function () use ($abstract) {
            return new $abstract;
        };
    }

    public static function make(string $key)
    {
        if (!DI::$initialized) {
            DI::init();
            DI::$initialized = true;
        }
        $container = DI::$containers[$key] ?? null;
        if ($container == null) {
            throw new BindingResolutionException('Failed to resolve [' . $key . ']');
        }
        if (is_callable($container)) {
            $container = $container();
            if (DI::$singletons[$key]) {
                DI::$containers[$key] = $container;
            }
        }
        return $container;
    }
}