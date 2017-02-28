<?php

namespace Isswp101\Persimmon\Repository;

use Isswp101\Persimmon\Collection\CollectionInterface;
use Isswp101\Persimmon\Model\ModelInterface;
use Isswp101\Persimmon\QueryBuilder\QueryBuilderInterface;

interface RepositoryInterface
{
    public function find($id): ModelInterface;

    public function all(QueryBuilderInterface $query): CollectionInterface;

    public function insert(ModelInterface $model);

    public function update(ModelInterface $model);

    public function delete($id);
}