<?php

namespace Isswp101\Persimmon\Exceptions;

use Exception;

class ParentModelNotFoundException extends Exception
{
    public function __construct(string $class, string $id)
    {
        $message = sprintf(
            'Model [%s] not found by id [%s].' .
            'Try to set parent id in your model or use inner_hits feature.',
            $class, $id
        );
        parent::__construct($message);
    }
}
