<?php

namespace Isswp101\Persimmon\DTO;

final class Path
{
    private string $index;
    private string|null $type;
    private Id $id;

    public function __construct(string $index, string|null $type, Id $id)
    {
        $this->index = $index;
        $this->type = $type;
        $this->id = $id;
    }

    public function getIndex(): string
    {
        return $this->index;
    }

    public function getType(): string|null
    {
        return $this->type;
    }

    public function getId(): Id
    {
        return $this->id;
    }
}
