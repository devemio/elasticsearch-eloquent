<?php

namespace Isswp101\Persimmon\Cache;

use Isswp101\Persimmon\Model\IEloquent;

class EloquentCache implements IEloquentCache
{
    private $cachedAttributes = [];

    public function get(): IEloquent
    {
        // TODO: Implement get() method.
    }

    public function put(IEloquent $model, array $cachedAttributes = [])
    {
        // TODO: Implement put() method.
    }

    public function getCachedAttributes(): array
    {
        return $this->cachedAttributes;
    }

    public function addCachedAtributes(array $cachedAttributes)
    {
        // TODO: Implement addCachedAtributes() method.
    }
}