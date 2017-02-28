<?php

namespace Isswp101\Persimmon\Model;

interface ModelInterface
{
    public static function find($id, array $columns): ModelInterface;

    public function save();

    public function delete();
}