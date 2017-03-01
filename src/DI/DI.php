<?php

namespace Isswp101\Persimmon\DI;

use Isswp101\Persimmon\Model\BaseModel;
use Isswp101\Persimmon\Repository\ElasticsearchRepository;

final class DI
{
    private static $containers = [];

    private static function init()
    {
        DI::$containers = [
            BaseModel::class => function () {
                return new Container(new ElasticsearchRepository(null));
            }
        ];
    }

    public static function make($class): Container
    {
        if ($class == null) {
            return null;
        }
        if (!DI::$containers) {
            DI::init();
        }
        $fn = DI::$containers[BaseModel::class];
        return $fn();
    }
}