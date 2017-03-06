<?php

namespace Isswp101\Persimmon\Collection;

use ArrayAccess;
use ArrayIterator;
use Countable;
use Isswp101\Persimmon\Contracts\Jsonable;
use Isswp101\Persimmon\Contracts\Stringable;
use IteratorAggregate;
use JsonSerializable;

class Collection implements ArrayAccess, Countable, IteratorAggregate, ICollection, Jsonable, Stringable, JsonSerializable
{
    protected $items = [];

    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    public function put($key, $value)
    {
        $this->offsetSet($key, $value);
        return $this;
    }

    public function get($key, $default = null)
    {
        return $this->offsetExists($key) ? $this->items[$key] : $default;
    }

    public function has($key)
    {
        return $this->offsetExists($key);
    }

    public function forget($key)
    {
        $this->offsetUnset($key);
        return $this;
    }

    public function all(): array
    {
        return $this->items;
    }

    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    public function isNotEmpty(): bool
    {
        return !$this->isEmpty();
    }

    public function first()
    {
        return array_values($this->items)[0] ?? null;
    }

    public function last()
    {
        return end($this->items);
    }

    public function jsonSerialize()
    {
        return $this->all();
    }

    public function toJson(int $options = 0): string
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function offsetExists($key)
    {
        return array_key_exists($key, $this->items);
    }

    public function offsetGet($key)
    {
        return $this->items[$key];
    }

    public function offsetSet($key, $value)
    {
        if (is_null($key)) {
            $this->items[] = $value;
        } else {
            $this->items[$key] = $value;
        }
    }

    public function offsetUnset($key)
    {
        unset($this->items[$key]);
    }

    public function __toString(): string
    {
        return $this->toJson();
    }

    public function each(callable $callback)
    {
        foreach ($this->items as $key => $item) {
            if ($callback($item, $key) === false) {
                break;
            }
        }
        return $this;
    }
}