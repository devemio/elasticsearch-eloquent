<?php

namespace Isswp101\Persimmon\DI;

use Isswp101\Persimmon\Repository\RepositoryInterface;

class Container
{
    private $repository;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getRepository(): RepositoryInterface
    {
        return $this->repository;
    }
}