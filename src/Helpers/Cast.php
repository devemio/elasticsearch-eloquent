<?php

namespace Isswp101\Persimmon\Helpers;

use Isswp101\Persimmon\Contracts\Storable;

class Cast
{
    public static function storable($instance): Storable
    {
        return $instance;
    }
}