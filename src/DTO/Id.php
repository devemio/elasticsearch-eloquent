<?php

namespace Isswp101\Persimmon\DTO;

final class Id
{
    private mixed $value;

    public function __construct(mixed $value)
    {
        $this->value = $value;
    }

    public function value(): mixed
    {
        return $this->value;
    }
}
