<?php

namespace Isswp101\Persimmon\Model;

use Isswp101\Persimmon\DI\Container;
use Isswp101\Persimmon\DI\DI;

class ElasticsearchModel extends Eloquent implements IElasticsearchModel
{
    protected static function di(): Container
    {
        return DI::make(DI::ELASTICSEARCH);
    }
}