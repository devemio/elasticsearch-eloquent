<?php

namespace Isswp101\Persimmon\Traits;

trait Containerable
{
    protected $attributes = [];

    public function __get($name)
    {
        return array_key_exists($name, $this->attributes) ? $this->attributes[$name] : null;
    }

    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    public function __isset($name)
    {
        return isset($this->attributes[$name]);
    }

    public function __unset($name)
    {
        unset($this->attributes[$name]);
    }

    public function fill(array $attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }

    public function toArray(): array
    {
        return $this->attributes;
    }

    public function toJson(int $options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }

    public function __toString(): string
    {
        return static::class . $this->toJson();
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}