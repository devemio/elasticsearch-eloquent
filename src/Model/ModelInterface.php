<?php

namespace Isswp101\Persimmon\Model;

interface ModelInterface
{
    public static function find($id, array $columns = null): ModelInterface;

    public static function findOrFail($id, array $columns = null): ModelInterface;

    public static function create(array $attributes): ModelInterface;

    public static function destroy($id);

    public function save(array $columns = null);

    public function delete();
}