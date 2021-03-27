<?php

namespace Isswp101\Persimmon\Contracts;

use Isswp101\Persimmon\Exceptions\ModelNotFoundException;
use Isswp101\Persimmon\Models\BaseElasticsearchModel;

interface Persistencable
{
    public function createPersistence(): PersistenceContract;

    public function save(): void;

    public function delete(): void;

    public static function create(array $attributes): BaseElasticsearchModel;

    public static function find(int|string $id, array $columns = []): BaseElasticsearchModel|null;

    /**
     * @param int|string $id
     * @param array $columns
     * @return BaseElasticsearchModel
     * @throws ModelNotFoundException
     */
    public static function findOrFail(int|string $id, array $columns = []): BaseElasticsearchModel;

    public static function destroy(int|string $id): void;

    public static function search(array $query): array;

    public static function first(array $query): BaseElasticsearchModel|null;

    /**
     * @param array $query
     * @return BaseElasticsearchModel
     * @throws ModelNotFoundException
     */
    public static function firstOrFail(array $query): BaseElasticsearchModel;

    public static function all(array $query): array;
}
