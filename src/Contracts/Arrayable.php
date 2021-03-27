<?php

namespace Isswp101\Persimmon\Contracts;

interface Arrayable
{
    public function toArray(array $keys): array;
}
