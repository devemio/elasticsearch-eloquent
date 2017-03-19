<?php

namespace Isswp101\Persimmon\Repository;

use Isswp101\Persimmon\Collection\Collection;
use Isswp101\Persimmon\Collection\ICollection;
use Isswp101\Persimmon\Contracts\Storable;
use Isswp101\Persimmon\Exceptions\ClassTypeErrorException;
use Isswp101\Persimmon\Helpers\Cast;
use Isswp101\Persimmon\QueryBuilder\IQueryBuilder;

class RuntimeCacheRepository implements ICacheRepository
{
    private $data = [];
    private $allColumns = [];

    public function __construct()
    {
        // $this->collection = new Collection();
        // $this->allColumns = new Collection();
    }

    private function getHash(string $class, string $id): string
    {
        return $class . '@' . $id;
    }

    public function instantiate(string $class): Storable
    {
        $instance = new $class;
        if (!$instance instanceof Storable) {
            throw new ClassTypeErrorException(Storable::class);
        }
        return $instance;
    }

    public function find(string $id, string $class, array $columns = []): ?Storable
    {
        $hash = $this->getHash($class, $id);
        $data = $this->data[$hash] ?? [];
        if ($data) {
            $model = $this->instantiate($class);
            $model->setPrimaryKey($id);
            if ($columns) {
                $data = array_intersect_key($data, array_flip($columns));
            }
            $model->fill($data);
            return $model;
        }
        return null;

        // if (!$columns && !$this->hasAllColumns($id, $class)) {
        //     return null;
        // }
        // $cachedModel = $this->collection->get($this->getHash($class, $id));
        // if ($cachedModel != null && $columns) {
        //     $cachedModel = Cast::storable($cachedModel);
        //     $model = $this->instantiate($class);
        //     $model->setPrimaryKey($cachedModel->getPrimaryKey());
        //     $model->fill(array_intersect_key($cachedModel->toArray(), array_flip($columns)));
        //     return $model;
        // }
        // return $cachedModel;
    }

    public function all(IQueryBuilder $query, string $class, callable $callback = null): ICollection
    {
        return new Collection($this->data); // @TODO return data that's related to $class
        // @TODO return collection of models, not array
    }

    public function insert(Storable $model): void
    {
        $hash = $this->getHash(get_class($model), $model->getPrimaryKey());
        $this->data[$hash] = $model->toArray();
    }

    public function update(Storable $model): void
    {
        $hash = $this->getHash(get_class($model), $model->getPrimaryKey());
        $this->data[$hash] = array_merge($this->data[$hash] ?? [], $model->toArray());
    }

    public function delete(Storable $model): void
    {
        $hash = $this->getHash(get_class($model), $model->getPrimaryKey());
        unset($this->data[$hash]);
    }

    public function hasAllColumns(string $id, string $class): bool
    {
        return $this->allColumns->has($this->getHash($class, $id));
    }

    public function setAllColumns(string $id, string $class): void
    {
        $this->allColumns->put($this->getHash($class, $id), true);
    }
}