<?php

namespace Isswp101\Persimmon\Exceptions;

use Exception;

class ClassTypeErrorException extends Exception
{
    public function __construct(string $class)
    {
        parent::__construct('Incompatible class type with ' . $class);
    }
}