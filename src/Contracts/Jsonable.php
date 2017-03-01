<?php

namespace Isswp101\Persimmon\Contracts;

interface Jsonable
{
    /**
     * Get the instance as json.
     *
     * @param int $options
     * @return string
     */
    public function toJson(int $options = 0): string;
}
