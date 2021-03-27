<?php

namespace Isswp101\Persimmon\Concerns;

trait Attributable
{
    private array $attributes;

    public function __get(string $key): mixed
    {
        return $this->attributes[$key] ?? null;
    }

    public function __set(string $key, mixed $value): void
    {
        $this->attributes[$key] = $value;
    }

    public function fill(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    public function toArray(): array
    {
        return $this->attributes;
    }

    public function __toString(): string
    {
        return json_encode($this->toArray());
    }
}
