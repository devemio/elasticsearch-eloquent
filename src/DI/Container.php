<?php

namespace Isswp101\Persimmon\DI;

use Isswp101\Persimmon\Cache\ICache;
use Isswp101\Persimmon\Repository\IRepository;

class Container
{
    private $repository;
    private $cache;

    public function __construct(IRepository $repository, ICache $cache)
    {
        $this->repository = $repository;
        $this->cache = $cache;
    }

    public function getRepository(): IRepository
    {
        return $this->repository;
    }

    public function getCache(): ICache
    {
        return $this->cache;
    }
}