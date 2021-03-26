<?php

namespace Isswp101\Persimmon\Concerns;

trait Elasticsearchable
{
    protected string $index;
    protected string $type;

    public function getIndex(): string
    {
        return $this->index;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
