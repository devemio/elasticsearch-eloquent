<?php

namespace Isswp101\Persimmon\Repository;

use Isswp101\Persimmon\Collection\ICollection;
use Isswp101\Persimmon\Contracts\Storable;
use Isswp101\Persimmon\Model\IEloquent;
use Isswp101\Persimmon\QueryBuilder\IQueryBuilder;

interface IRepository
{
    public function find($id, string $class, array $columns = null): IEloquent;

    public function all(IQueryBuilder $query, string $class, array $columns = null): ICollection;

    public function insert(Storable $model);

    public function update(Storable $model);

    public function delete($id);
}