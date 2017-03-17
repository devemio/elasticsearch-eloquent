<?php

namespace Isswp101\Persimmon\Repository;

use Isswp101\Persimmon\Collection\ICollection;
use Isswp101\Persimmon\Contracts\Storable;
use Isswp101\Persimmon\QueryBuilder\IQueryBuilder;

interface IRepository
{
    public function instantiate(string $class): Storable;

    public function find(string $id, string $class, array $columns = []): ?Storable;

    public function all(IQueryBuilder $query, string $class, callable $callback = null): ICollection;

    public function insert(Storable $model): void;

    public function update(Storable $model): void;

    public function delete(Storable $model): void;
}