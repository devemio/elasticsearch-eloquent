<?php

namespace Isswp101\Persimmon\Concerns;

trait Elasticsearchable
{
    protected string $index;
    protected string|null $type = null;

    public function getIndex(): string
    {
        return $this->index;
    }

    public function getType(): string|null
    {
        return $this->type;
    }
}
