<?php

namespace Isswp101\Persimmon\Relationship;

class RelationshipKey
{
    const DELIMITER = ':';

    private $id;
    private $parentId;

    public function __construct(string $id, string $parentId = null)
    {
        $this->id = $id;
        $this->parentId = $parentId;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getParentId()
    {
        return $this->parentId;
    }

    public function build(): string
    {
        $id = $this->getId();
        return $this->getParentId() != null ? $id . RelationshipKey::DELIMITER . $this->getParentId() : $id;
    }

    public static function parse(string $key): RelationshipKey
    {
        $ids = explode(RelationshipKey::DELIMITER, $key);
        return new RelationshipKey($ids[0] ?? null, $ids[1] ?? null);
    }
}