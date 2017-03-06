<?php

namespace Isswp101\Persimmon\Exceptions;

use Exception;

class IllegalCollectionException extends Exception
{
    public function __construct()
    {
        parent::__construct('Const [collection] must be overridden in your model');
    }
}