<?php

namespace Isswp101\Persimmon\QueryBuilder;

interface IQueryBuilder
{
    public function chunk(int $count): IQueryBuilder;

    public function build(): array;
}