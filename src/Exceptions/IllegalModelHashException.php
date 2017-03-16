<?php

namespace Isswp101\Persimmon\Exceptions;

use Exception;
use Isswp101\Persimmon\Model\IEloquent;

class IllegalModelHashException extends Exception
{
    public function __construct(IEloquent $model)
    {
        parent::__construct("Model [" . get_class($model) . "] can't be hashable. You need to set a primary key.");
    }
}