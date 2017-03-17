<?php

namespace Isswp101\Persimmon\Model;

use Isswp101\Persimmon\Collection\ICollection;
use Isswp101\Persimmon\DI\Container;
use Isswp101\Persimmon\Exceptions\IllegalCollectionException;
use Isswp101\Persimmon\Exceptions\IllegalModelHashException;
use Isswp101\Persimmon\Exceptions\ModelNotFoundException;
use Isswp101\Persimmon\QueryBuilder\IQueryBuilder;
use Isswp101\Persimmon\Traits\Containerable;
use Isswp101\Persimmon\Traits\Eventable;
use Isswp101\Persimmon\Traits\Timestampable;

/**
 * @TODO
 * 1. Cache
 * 2. Consider columns when searching
 * 3. Check __clone()
 */
abstract class Eloquent implements IEloquent
{
    use Containerable, Timestampable, Eventable;

    protected $primaryKey;
    protected $exists = false;
    protected $timestamps = false;
    protected $cache = false;

    /** @MustBeOverridden */
    const COLLECTION = null;

    const PRIMARY_KEY = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    abstract protected static function di(): Container;

    protected static function instantiate(string $primaryKey): IEloquent
    {
        $model = new static();
        $model->setPrimaryKey($primaryKey);
        return $model;
    }

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }

    public function setPrimaryKey(string $key)
    {
        $this->primaryKey = $key;
    }

    public function getPrimaryKey(): string
    {
        if ($this->primaryKey == null) {
            $this->setPrimaryKey($this->{static::PRIMARY_KEY});
        }
        return $this->primaryKey;
    }

    final public static function getCollection(): string
    {
        if (static::COLLECTION == null) {
            throw new IllegalCollectionException();
        }
        return static::COLLECTION;
    }

    public function exists(bool $value = null): bool
    {
        if ($value != null) {
            $this->exists = $value;
        }
        return $this->exists;
    }

    public static function all(IQueryBuilder $query, callable $callback = null): ICollection
    {
        $collection = static::di()->getRepository()->all($query, static::class,
            function (IEloquent $model) use ($callback) {
                $model->exists(true);
                if ($callback != null) {
                    $callback($model);
                }
            });
        return $collection;
    }

    public static function find(string $id, array $columns = []): ?IEloquent
    {
        $di = static::di();
//        $model = static::instantiate($id);

//        if ($model->shouldUseCache()) {
//            $cache = $di->getCache()->get($model->getHash());
//            $columns = array_diff($columns, $cache->getCachedAttributes());
//            if (!$columns && $cache->get() != null) {
//                return $cache->get();
//            }
//        }
//
//        $cache = $di->getCacheRepository()->find($id, static::class, $columns);
//        if ($cache->isReturnable()) {
//
//        }


        $model = $di->getRepository()->find($id, static::class, $columns);
        if ($model != null) {
            $model->exists = true;
//            if ($model->shouldUseCache()) {
//                $cache->put($model, $columns);
//            }
        }
        return $model;
    }

    public static function findOrFail(string $id, array $columns = []): IEloquent
    {
        $model = static::find($id, $columns);
        if ($model == null) {
            throw new ModelNotFoundException(get_called_class(), $id);
        }
        return $model;
    }

    public static function create(array $attributes): IEloquent
    {
        $model = new static($attributes);
        $model->save();
        return $model;
    }

    public static function destroy(string $id): void
    {
        static::findOrFail($id)->delete();
    }

    public function save(array $columns = []): void
    {
        if ($this->saving() === false) {
            return;
        }
        $repository = $this->di()->getRepository();
        if ($this->timestamps) {
            $this->updateTimestamps();
        }
        if (!$this->exists) {
            $repository->insert($this);
        } else {
            $repository->update($this);
        }
        $this->exists = true;
        if ($this->saved() === false) {
            return;
        }
    }

    public function delete(): void
    {
        if ($this->deleting() === false) {
            return;
        }
        $this->di()->getRepository()->delete($this);
        $this->exists = false;
        if ($this->deleted() === false) {
            return;
        }
    }

    public function getHash(): string
    {
        if ($this->getPrimaryKey() == null) {
            throw new IllegalModelHashException($this);
        }
        return get_class($this) . '@' . $this->getPrimaryKey();
    }

    public function shouldUseCache(): bool
    {
        return $this->cache;
    }
}