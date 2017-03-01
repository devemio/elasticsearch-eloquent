<?php

namespace Isswp101\Persimmon\Exceptions;

use Exception;
use ReflectionClass;

class ModelNotFoundException extends Exception
{
    public function __construct($class, $id = null)
    {
        $reflection = new ReflectionClass($class);
        $model = $reflection->getShortName();
        if ($id != null) {
            $message = sprintf('Model [%s] not found by id [%s]', $model, $id);
        } else {
            $message = sprintf('Model [%s] not found', $model);
        }
        parent::__construct($message);
    }
}
