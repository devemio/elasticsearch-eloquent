<?php

namespace Isswp101\Persimmon\QueryBuilder;

interface IQueryBuilder
{
    public function build(): array;

    public function setChunkCount(int $count): IQueryBuilder;

    public function getChunkCount(): int;
}