<?php

namespace Isswp101\Persimmon\Exceptions;

use Exception;

class ModelNotFoundException extends Exception
{
    public function __construct()
    {
        parent::__construct('Model not found');
    }
}
