<?php

namespace Isswp101\Persimmon\DTO;

final class Path
{
    private string $index;
    private string $type;
    private Id $id;

    public function __construct(string $index, string $type, Id $id)
    {
        $this->index = $index;
        $this->type = $type;
        $this->id = $id;
    }

    public function getIndex(): string
    {
        return $this->index;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getId(): Id
    {
        return $this->id;
    }
}
