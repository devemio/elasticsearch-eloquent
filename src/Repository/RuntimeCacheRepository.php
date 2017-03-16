<?php

namespace Isswp101\Persimmon\Repository;

use Isswp101\Persimmon\Collection\Collection;
use Isswp101\Persimmon\Contracts\Storable;
use Isswp101\Persimmon\Exceptions\ClassTypeErrorException;
use Isswp101\Persimmon\Model\IEloquent;
use Isswp101\Persimmon\QueryBuilder\IQueryBuilder;

class RuntimeCacheRepository implements ICacheRepository
{
    private $collection;

    public function __construct()
    {
        $this->collection = new Collection();
    }

    private function getHash(string $class, string $id): string
    {
        return $class . '@' . $id;
    }

    public function instantiate(string $class): Storable
    {
        $instance = new $class;
        if (!$instance instanceof IEloquent) {
            throw new ClassTypeErrorException(IEloquent::class);
        }
        return $instance;
    }

    public function find($id, string $class, array $columns = []): Storable
    {
        $model = $this->collection->get($this->getHash($class, $id));
        if ($model != null && $columns) {
            return $model->fill(array_intersect_key($model->toArray(), array_flip($columns)));
        }
        return $model;
    }

    public function all(IQueryBuilder $query, string $class, callable $callback = null)
    {
        // TODO: Implement all() method.
    }

    public function insert(Storable $model)
    {
        // TODO: Implement insert() method.
    }

    public function update(Storable $model)
    {
        // TODO: Implement update() method.
    }

    public function delete(Storable $model)
    {
        // TODO: Implement delete() method.
    }

    public function getCachedAttributes(string $id, string $class): array
    {
        // TODO: Implement getCachedAttributes() method.
    }
}