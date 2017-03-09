<?php

namespace Isswp101\Persimmon\Collection;

interface IElasticsearchCollection extends ICollection
{
    public function getTook(): int;

    public function isTimedOut(): bool;

    public function getTotal(): int;

    public function getMaxScore(): int;

    public function getShards(): array;

    public function getAggregation(string $name): array;
}