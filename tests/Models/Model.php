<?php

namespace Isswp101\Persimmon\Models;

use Isswp101\Persimmon\DI\Container;
use Isswp101\Persimmon\DI\DI;
use Isswp101\Persimmon\Model\BaseModel;
use Isswp101\Persimmon\Model\IElasticsearchModel;

class Model extends BaseModel implements IElasticsearchModel
{
    const collection = 'my_index/my_type';

    protected static function di(): Container
    {
        return DI::make(DI::ELASTICSEARCH);
    }
}