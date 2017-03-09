<?php

namespace Isswp101\Persimmon\Model;

use Isswp101\Persimmon\Contracts\Storable;
use Isswp101\Persimmon\Contracts\Presentable;

interface IEloquent extends Presentable, Storable
{
    public function exists(bool $value = null): bool;

    public static function find($id, array $columns = []): IEloquent;

    public static function findOrFail($id, array $columns = []): IEloquent;

    public static function create(array $attributes): IEloquent;

    public static function destroy($id);

    public function save(array $columns = []);

    public function delete();
}