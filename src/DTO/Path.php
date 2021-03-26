<?php

namespace Isswp101\Persimmon\DTO;

use Stringable;

final class Path implements Stringable
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

    public function __toString(): string
    {
        if (!$this->id->value()) {
            return implode('/', [$this->index, $this->type]);
        }

        return implode('/', [$this->index, $this->type, $this->id->value()]);
    }
}
