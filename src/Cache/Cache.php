<?php

namespace Isswp101\Persimmon\Cache;

use Isswp101\Persimmon\Collection\Collection;

class Cache implements ICache
{
    private $collection;

    public function __construct()
    {
        $this->collection = new Collection();
    }

    public function get(string $hash): IEloquentCache
    {
        if (!$this->collection->has($hash)) {
            $this->collection->put($hash, new EloquentCache());
        }
        return $this->collection->get($hash);
    }
}