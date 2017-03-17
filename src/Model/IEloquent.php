<?php

namespace Isswp101\Persimmon\Model;

use Isswp101\Persimmon\Collection\ICollection;
use Isswp101\Persimmon\Contracts\Presentable;
use Isswp101\Persimmon\Contracts\Storable;
use Isswp101\Persimmon\QueryBuilder\IQueryBuilder;

interface IEloquent extends Presentable, Storable
{
    public function exists(bool $value = null): bool;

    public static function all(IQueryBuilder $query, callable $callback = null): ICollection;

    public static function find(string $id, array $columns = []): ?IEloquent;

    public static function findOrFail(string $id, array $columns = []): IEloquent;

    public static function create(array $attributes): IEloquent;

    public static function destroy(string $id): void;

    public function save(array $columns = []): void;

    public function delete(): void;
}