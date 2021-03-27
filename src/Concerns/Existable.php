<?php

namespace Isswp101\Persimmon\Concerns;

trait Existable
{
    private bool $exists = false;

    public function exists(): bool
    {
        return $this->exists;
    }
}
