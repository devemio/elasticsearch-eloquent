<?php

namespace Isswp101\Persimmon\Models;

use Elasticsearch\ClientBuilder;
use Isswp101\Persimmon\Concerns\Elasticsearchable;
use Isswp101\Persimmon\Concerns\Timestampable;
use Isswp101\Persimmon\Contracts\Arrayable;
use Isswp101\Persimmon\Contracts\ElasticsearchModelContract;
use Isswp101\Persimmon\Contracts\Persistencable;
use Isswp101\Persimmon\Contracts\PersistenceContract;
use Isswp101\Persimmon\DTO\Id;
use Isswp101\Persimmon\DTO\Path;
use Isswp101\Persimmon\Exceptions\ModelNotFoundException;
use Isswp101\Persimmon\Persistence\Persistence;
use Stringable;

/**
 * @property int|string|null id
 * @property string created_at
 * @property string updated_at
 */
abstract class BaseElasticsearchModel implements ElasticsearchModelContract, Persistencable, Arrayable, Stringable
{
    use Elasticsearchable, Timestampable;

    private PersistenceContract $persistence;

    private array $attributes;
    private bool $existing = false;

    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;

        $this->persistence = $this->createPersistence();
    }

    public function createPersistence(): PersistenceContract
    {
        $client = ClientBuilder::create()->build();

        return new Persistence($client);
    }

    public function __get(string $key): mixed
    {
        return $this->attributes[$key] ?? null;
    }

    public function __set(string $key, mixed $value): void
    {
        $this->attributes[$key] = $value;
    }

    public function getId(): int|string|null
    {
        return $this->id;
    }

    public function toArray(): array
    {
        return $this->attributes;
    }

    public function fill(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    public function __toString(): string
    {
        return json_encode($this->toArray());
    }

    public function save(): void
    {
        $path = new Path($this->index, $this->type, new Id($this->id));

        $this->touch();

        $this->id = $this->persistence->save($path, $this->toArray())->value();

        $this->existing = true;
    }

    public function delete(): void
    {
        $path = new Path($this->index, $this->type, new Id($this->id));

        $this->persistence->delete($path);
    }

    public static function create(array $attributes): static
    {
        $model = new static($attributes);

        $model->save();

        return $model;
    }

    public static function find(int|string $id): static|null
    {
        $model = new static();

        $path = new Path($model->getIndex(), $model->getType(), new Id($id));

        $attributes = $model->persistence->find($path);

        if (!$attributes) {
            return null;
        }

        $model->fill($attributes);

        $model->id = $id;

        $model->existing = true;

        return $model;
    }

    public static function findOrFail(int|string $id): static
    {
        $model = static::find($id);

        if (!$model) {
            throw new ModelNotFoundException();
        }

        return $model;
    }

    public static function destroy(int|string $id): void
    {
        $model = new static();

        $model->id = $id;

        $model->delete();
    }
}
