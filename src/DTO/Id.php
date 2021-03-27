<?php

namespace Isswp101\Persimmon\DTO;

final class Id
{
    private int|string|null $value;

    public function __construct(int|string|null $value)
    {
        $this->value = $value;
    }

    public function value(): int|string|null
    {
        return $this->value;
    }

    public function isPresent(): bool
    {
        return $this->value != null;
    }

    public static function undefined(): Id
    {
        return new Id(null);
    }
}
