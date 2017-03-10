<?php

namespace Isswp101\Persimmon\Relationship;

class RelationshipKey
{
    const DELIMITER = ':';

    private $id;
    private $parentId;

    public function __construct(string $id)
    {
        $ids = explode(RelationshipKey::DELIMITER, $id);
        $this->id = $ids[0] ?? null;
        $this->parentId = $ids[1] ?? null;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getParentId()
    {
        return $this->parentId;
    }
}