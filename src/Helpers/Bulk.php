<?php

namespace Isswp101\Persimmon\Helpers;

use Isswp101\Persimmon\DI\DI;
use Isswp101\Persimmon\Model\IElasticsearchModel;

class Bulk
{
    /**
     * @param IElasticsearchModel[] $models
     */
    public static function index(array $models)
    {
        DI::make(DI::ELASTICSEARCH)->getRepository()->bulk($models);
    }
}