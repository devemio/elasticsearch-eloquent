<?php

namespace Isswp101\Persimmon\Model;

use Isswp101\Persimmon\Contracts\Presentable;
use Isswp101\Persimmon\Contracts\Storable;
use Isswp101\Persimmon\DI\Container;
use Isswp101\Persimmon\Exceptions\IllegalCollectionException;
use Isswp101\Persimmon\Exceptions\ModelNotFoundException;
use Isswp101\Persimmon\Traits\Containerable;
use Isswp101\Persimmon\Traits\Eventable;
use Isswp101\Persimmon\Traits\Timestampable;

/**
 *
 * @TODO
 * 1. Events +
 * 2. Timestamps +
 * 3. Cache
 * 4. Update __toString() +
 */
abstract class BaseModel implements IEloquent, Storable, Presentable
{
    use Containerable, Timestampable, Eventable;

    protected $exists = false;
    protected $timestamps = false;

    /** @MustBeOverridden */
    const collection = null;

    const PRIMARY_KEY = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    abstract protected static function di(): Container;

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }

    public function getPrimaryKey(): string
    {
        return $this->{static::PRIMARY_KEY};
    }

    public static function getCollection(): string
    {
        if (static::collection == null) {
            throw new IllegalCollectionException();
        }
        return static::collection;
    }

    public function exists(bool $value = null): bool
    {
        if ($value != null) {
            $this->exists = $value;
        }
        return $this->exists;
    }

    public static function find($id, array $columns = null): IEloquent
    {
        $model = static::di()->getRepository()->find($id, static::class, $columns);
        if ($model != null) {
            $model->exists = true;
        }
        return $model;
    }

    public static function findOrFail($id, array $columns = null): IEloquent
    {
        $model = static::find($id, $columns);
        if ($model == null) {
            throw new ModelNotFoundException(get_called_class(), $id);
        }
        return $model;
    }

    public static function create(array $attributes): IEloquent
    {
        return null;
    }

    public static function destroy($id)
    {
        return null;
    }

    public function save(array $columns = null)
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

    public function delete()
    {
        if ($this->deleting() === false) {
            return;
        }
        $this->di()->getRepository()->delete($this->getId());
        $this->exists = false;
        if ($this->deleted() === false) {
            return;
        }
    }
}