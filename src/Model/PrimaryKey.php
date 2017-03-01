<?php

namespace Isswp101\Persimmon\Model;

class PrimaryKey implements PrimaryKeyInterface
{
    private $key;

    public function __construct($key)
    {
        $this->key = $key;
    }

    public function get()
    {
        return $this->key;
    }
}