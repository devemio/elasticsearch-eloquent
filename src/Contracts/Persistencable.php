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

    public static function find(int|string $id): BaseElasticsearchModel|null;

    /**
     * @param int|string $id
     * @return BaseElasticsearchModel
     * @throws ModelNotFoundException
     */
    public static function findOrFail(int|string $id): BaseElasticsearchModel;

    public static function destroy(int|string $id): void;
}
