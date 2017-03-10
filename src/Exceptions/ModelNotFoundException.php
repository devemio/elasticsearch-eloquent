<?php

namespace Isswp101\Persimmon\Exceptions;

use Exception;
use Isswp101\Persimmon\Helpers\Reflection;

class ModelNotFoundException extends Exception
{
    public function __construct(string $class, $id = null)
    {
        if ($id != null) {
            $message = sprintf('Model [%s] not found by id [%s]', $class, $id);
        } else {
            $message = sprintf('Model [%s] not found', $class);
        }
        parent::__construct($message);
    }
}
