<?php

namespace Isswp101\Persimmon\Aggregations;

class Bucket
{
    private $key;
    private $docCount;

    public function __construct(string $key, int $docCount)
    {
        $this->key = $key;
        $this->docCount = $docCount;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getDocCount(): int
    {
        return $this->docCount;
    }
}