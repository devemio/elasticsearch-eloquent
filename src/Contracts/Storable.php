<?php

namespace Isswp101\Persimmon\Contracts;

interface Storable extends Arrayable
{
    public function fill(array $attributes);
}
