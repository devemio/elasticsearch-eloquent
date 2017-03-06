<?php

namespace Isswp101\Persimmon\DI;

use Elasticsearch\ClientBuilder;
use Isswp101\Persimmon\Repository\ElasticsearchRepository;

final class DI
{
    const ELASTICSEARCH = 'elasticsearch';

    private static $containers = [];

    private static function init(): array
    {
        return DI::$containers = [
            DI::ELASTICSEARCH => function () {
                $client = ClientBuilder::create()->build();
                return new Container(new ElasticsearchRepository($client));
            }
        ];
    }

    public static function make(string $name): Container
    {
        $containers = !DI::$containers ? DI::init() : DI::$containers;
        return $containers[$name]();
    }
}