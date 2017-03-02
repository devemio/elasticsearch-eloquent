<?php

namespace Isswp101\Persimmon\Models;

use Isswp101\Persimmon\DI\Container;
use Isswp101\Persimmon\Model\BaseModel;

class Model extends BaseModel
{
    const collection = 'my_index/my_type';

    protected static function di(): Container
    {
        return null;
    }
}