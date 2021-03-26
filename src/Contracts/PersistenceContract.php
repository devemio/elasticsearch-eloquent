<?php

namespace Isswp101\Persimmon\Contracts;

use Isswp101\Persimmon\DTO\Path;

interface PersistenceContract
{
    public function find(Path $path): array;

    public function save(Path $path, array $attributes): mixed;

    public function delete(Path $path): void;
}
