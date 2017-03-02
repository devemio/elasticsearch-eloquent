<?php

namespace Isswp101\Persimmon\Model;

interface IEloquent
{
    public function exists(bool $value = null): bool;

    public static function find($id, array $columns = null): IEloquent;

    public static function findOrFail($id, array $columns = null): IEloquent;

    public static function create(array $attributes): IEloquent;

    public static function destroy($id);

    public function save(array $columns = null);

    public function delete();
}