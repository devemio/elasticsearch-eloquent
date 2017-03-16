<?php

namespace Isswp101\Persimmon\Cache;

use Isswp101\Persimmon\Model\IEloquent;

interface IEloquentCache
{
    public function get(): IEloquent;

    public function put(IEloquent $model, array $cachedAttributes = []);

    public function getCachedAttributes(): array;

    public function addCachedAtributes(array $cachedAttributes);
}