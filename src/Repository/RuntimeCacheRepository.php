<?php

namespace Isswp101\Persimmon\Repository;

use Isswp101\Persimmon\Collection\Collection;
use Isswp101\Persimmon\Collection\ICollection;
use Isswp101\Persimmon\Contracts\Storable;
use Isswp101\Persimmon\Exceptions\ClassTypeErrorException;
use Isswp101\Persimmon\Helpers\Cast;
use Isswp101\Persimmon\QueryBuilder\IQueryBuilder;

class RuntimeCacheRepository implements IRepository
{
    private $data = [];

    private function getHash(string $id, string $class): string
    {
        return $class . '@' . $id;
    }

    private function getHashByModel(Storable $model): string
    {
        return $this->getHash($model->getPrimaryKey(), get_class($model));
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
        $hash = $this->getHash($id, $class);
        $data = $this->data[$hash] ?? [];
        if (!$data) {
            return null;
        }
        $model = $this->instantiate($class);
        $model->setPrimaryKey($id);
        if ($columns) {
            $data = array_intersect_key($data, array_flip($columns));
        }
        $model->fill($data);
        return $model;
    }

    public function all(IQueryBuilder $query, string $class, callable $callback = null): ICollection
    {
        throw new MethodNotImplemented(__METHOD__);
    }

    public function insert(Storable $model): void
    {
        $hash = $this->getHashByModel($model);
        $this->data[$hash] = $model->toArray();
    }

    public function update(Storable $model): void
    {
        $hash = $this->getHashByModel($model);
        $this->data[$hash] = array_merge($this->data[$hash] ?? [], $model->toArray());
    }

    public function delete(Storable $model): void
    {
        $hash = $this->getHashByModel($model);
        unset($this->data[$hash]);
    }
}