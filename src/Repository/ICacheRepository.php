<?php

namespace Isswp101\Persimmon\Repository;

interface ICacheRepository extends IRepository
{
    public function getCachedAttributes(string $id, string $class): array;
}