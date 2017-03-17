<?php

namespace Isswp101\Persimmon\Repository;

interface ICacheRepository extends IRepository
{
    public function hasAllColumns(string $id, string $class): bool;

    public function setAllColumns(string $id, string $class): void;
}