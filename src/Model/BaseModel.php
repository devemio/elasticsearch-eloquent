<?php

namespace Isswp101\Persimmon\Model;

use Isswp101\Persimmon\Contracts\Arrayable;
use Isswp101\Persimmon\Contracts\Jsonable;
use Isswp101\Persimmon\Contracts\Stringable;
use Isswp101\Persimmon\DI\Container;
use Isswp101\Persimmon\Traits\Containerable;
use Isswp101\Persimmon\Traits\Eventable;
use Isswp101\Persimmon\Traits\Timestampable;
use JsonSerializable;

/**
 * @property mixed id Primary key
 * @property string created_at
 * @property string updated_at
 *
 * @TODO
 * 1. Events +
 * 2. Timestamps +
 * 3. Cache
 */
abstract class BaseModel implements ModelInterface, Arrayable, Jsonable, Stringable, JsonSerializable
{
    use Containerable, Timestampable, Eventable;

    protected $exists = false;
    protected $timestamps = false;

    abstract protected static function di(): Container;

    public static function getPrimaryKey($id): PrimaryKeyInterface
    {
        return new PrimaryKey($id);
    }

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }

    public function getId()
    {
        return $this->id;
    }

    public static function find($id, array $columns = null): ModelInterface
    {
        $model = static::di()->getRepository()->find($id, static::class, $columns);
        if ($model != null) {
            $model->exists = true;
        }
        return $model;
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