<?php

namespace Isswp101\Persimmon\Traits;

trait Eventable
{
    protected function saving(): bool
    {
        return true;
    }

    protected function saved(): bool
    {
        return true;
    }

    protected function deleting(): bool
    {
        return true;
    }

    protected function deleted(): bool
    {
        return true;
    }
}
