<?php

namespace Isswp101\Persimmon\Contracts;

interface ElasticsearchModelContract
{
    public function getIndex(): string;

    public function getType(): string;

    public function getId(): int|string|null;

    public function fill(array $attributes): void;
}
