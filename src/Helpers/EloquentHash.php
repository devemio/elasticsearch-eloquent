<?php

namespace Isswp101\Persimmon\Helpers;

class EloquentHash
{
    public static function make(string $class, string $id): string
    {
        return $class . '@' . $id;
    }
}