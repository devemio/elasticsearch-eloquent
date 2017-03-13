<?php

namespace Isswp101\Persimmon\Relationship;

use Isswp101\Persimmon\Exceptions\ParentModelNotFoundException;
use Isswp101\Persimmon\Model\IElasticsearchModel;

class BelongsToRelationship
{
    protected $child;

    /**
     * @var IElasticsearchModel
     */
    protected $parentClass;

    public function __construct(IElasticsearchModel $child, string $parentClass)
    {
        $this->child = $child;
        $this->parentClass = $parentClass;
    }

    public function associate(IElasticsearchModel $parent)
    {
        $relationshipKey = new RelationshipKey($this->child->getPrimaryKey(), $parent->getPrimaryKey());
        $this->child->setPrimaryKey($relationshipKey->build());
    }

    public function get(): IElasticsearchModel
    {
        $relationshipKey = RelationshipKey::parse($this->child->getPrimaryKey());
        return ($this->parentClass)::find($relationshipKey->getParentId());
    }

    public function getOrFail(): IElasticsearchModel
    {
        $parent = $this->get();
        if ($parent == null) {
            $relationshipKey = RelationshipKey::parse($this->child->getPrimaryKey());
            throw new ParentModelNotFoundException($this->parentClass, $relationshipKey->getParentId());
        }
        return $parent;
    }
}
