<?php

namespace Isswp101\Persimmon\Exceptions;

use Exception;
use Isswp101\Persimmon\Helpers\Reflection;

class ParentModelNotFoundException extends Exception
{
    public function __construct($class, $id)
    {
        $message = sprintf(
            'Model [%s] not found by id [%s].' .
            'Try to set parent id in your model or use inner_hits feature.',
            Reflection::getShortname($class), $id
        );
        parent::__construct($message);
    }
}
