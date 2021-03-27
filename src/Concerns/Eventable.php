<?php

namespace Isswp101\Persimmon\Concerns;

use Isswp101\Persimmon\DTO\SearchResponse;

trait Eventable
{
    protected function saving(): bool
    {
        return true;
    }

    protected function saved(): void
    {
    }

    protected function deleting(): bool
    {
        return true;
    }

    protected function deleted(): void
    {
    }

    protected function searching(): bool
    {
        return true;
    }

    protected function searched(SearchResponse $response): void
    {
    }
}
