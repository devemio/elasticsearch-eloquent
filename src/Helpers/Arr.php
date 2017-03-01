<?php

namespace Isswp101\Persimmon\Helpers;

class Arr
{
    public static function get(array $array, $key, $default = null)
    {
        return array_key_exists($key, $array) ? $array[$key] : $default;
    }
}