<?php

namespace Isswp101\Persimmon\Repository;

use Isswp101\Persimmon\Collection\Collection;
use Isswp101\Persimmon\Collection\ICollection;
use Isswp101\Persimmon\Contracts\Storable;
use Isswp101\Persimmon\Exceptions\MethodNotImplementedException;
use Isswp101\Persimmon\QueryBuilder\IQueryBuilder;

class RuntimeCacheRepository implements ICacheRepository
{
    /**
     * @var Storable[]
     */
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
        throw new MethodNotImplementedException();
    }

    public function find(string $id, string $class, array $columns = []): ?Storable
    {
        $model = $this->collection->get($this->getHash($class, $id));
        if ($model != null && $columns) {
            return $model->fill(array_intersect_key($model->toArray(), array_flip($columns)));
        }
        return $model;
    }

    public function all(IQueryBuilder $query, string $class, callable $callback = null): ICollection
    {
        return $this->collection;
    }

    public function insert(Storable $model): void
    {
        $hash = $this->getHash(get_class($model), $model->getPrimaryKey());
        $this->collection->put($hash, $model);
    }

    public function update(Storable $model): void
    {
        $hash = $this->getHash(get_class($model), $model->getPrimaryKey());
        if (!$this->collection->has($hash)) {
            $this->collection->put($hash, $model);
        } else {
            $cachedModel = $this->collection->get($hash);
            foreach ($model->toArray() as $key => $value) {
                $cachedModel->{$key} = $value;
            }
        }
    }

    public function delete(Storable $model): void
    {
        $hash = $this->getHash(get_class($model), $model->getPrimaryKey());
        $this->collection->forget($hash);
    }

    public function getCachedAttributes(string $id, string $class): array
    {
        $model = $this->find($id, $class);
        return $model != null ? array_keys($model->toArray()) : [];
    }
}