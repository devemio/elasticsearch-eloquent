<?php

namespace Isswp101\Persimmon\Repository;

use Isswp101\Persimmon\Exceptions\IllegalCollectionException;

class ElasticsearchCollectionParser
{
    private $index;
    private $type;

    public function __construct(string $collection)
    {
        $keys = explode('/', $collection);
        $this->index = $keys[0] ?? null;
        $this->type = $keys[1] ?? null;
        if ($this->index == null || $this->type == null) {
            throw new IllegalCollectionException();
        }
    }

    public function getIndex(): string
    {
        return $this->index;
    }

    public function getType(): string
    {
        return $this->type;
    }
}