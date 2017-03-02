<?php

namespace Isswp101\Persimmon\DI;

use Isswp101\Persimmon\Repository\IRepository;

class Container
{
    private $repository;

    public function __construct(IRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getRepository(): IRepository
    {
        return $this->repository;
    }
}