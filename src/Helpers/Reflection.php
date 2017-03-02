<?php

namespace Isswp101\Persimmon\Helpers;

use ReflectionClass;

class Reflection
{
    public static function getShortname(string $class): string
    {
        $reflection = new ReflectionClass($class);
        return $reflection->getShortName();
    }
}