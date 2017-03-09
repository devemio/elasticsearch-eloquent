<?php

namespace Isswp101\Persimmon\Contracts;

interface Storable extends Arrayable
{
    public static function getCollectionName(): string;

    public function getPrimaryKey(): string;

    public function setPrimaryKey(string $key);

    public function fill(array $attributes);
}
