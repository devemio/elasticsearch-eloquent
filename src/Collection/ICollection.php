<?php

namespace Isswp101\Persimmon\Collection;

interface ICollection
{
    public function put($key, $value);

    public function get($key, $default = null);

    public function has($key);

    public function forget($key);

    public function all(): array;

    public function isEmpty(): bool;

    public function isNotEmpty(): bool;

    public function first();

    public function last();

    public function each(callable $callback);
}