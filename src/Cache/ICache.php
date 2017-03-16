<?php

namespace Isswp101\Persimmon\Cache;

interface ICache
{
    public function get(string $hash): IEloquentCache;
}