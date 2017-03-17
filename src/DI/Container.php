<?php

namespace Isswp101\Persimmon\DI;

use Isswp101\Persimmon\Repository\ICacheRepository;
use Isswp101\Persimmon\Repository\IRepository;

class Container
{
    private $repository;
    private $cacheRepository;

    public function __construct(IRepository $repository, ICacheRepository $cacheRepository)
    {
        $this->repository = $repository;
        $this->cacheRepository = $cacheRepository;
    }

    public function getRepository(): IRepository
    {
        return $this->repository;
    }

    public function getCacheRepository(): ICacheRepository
    {
        return $this->cacheRepository;
    }
}