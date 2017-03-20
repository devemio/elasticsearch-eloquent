<?php

namespace Isswp101\Persimmon\Helpers;

class Arr
{
    public static function only(array $array, array $keys)
    {
        return array_intersect_key($array, array_flip($keys));
    }
}