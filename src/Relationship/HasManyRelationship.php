<?php

namespace Isswp101\Persimmon\Relationship;

use Isswp101\Persimmon\Collection\IElasticsearchCollection;
use Isswp101\Persimmon\ElasticsearchModel;
use Isswp101\Persimmon\Model\IElasticsearchModel;
use Isswp101\Persimmon\QueryBuilder\Filters\ParentFilter;
use Isswp101\Persimmon\QueryBuilder\QueryBuilder;

class HasManyRelationship
{
    protected $parent;
    protected $childClass;

    public function __construct(IElasticsearchModel $parent, string $childClass)
    {
        $this->parent = $parent;
        $this->childClass = $childClass;
    }

    public function get(): IElasticsearchCollection
    {
        return ($this->childClass)::all(new QueryBuilder());

        $child = $this->childClass;
        $query = new QueryBuilder();
        $query->filter(new ParentFilter($this->parent->getId()));
        $collection = $child::search($query);
        $collection->each(function (ElasticsearchModel $model) {
            $model->setParent($this->parent);
        });
        return $collection;
    }

    public function find($id): IElasticsearchModel
    {
        $relationshipKey = new RelationshipKey($id, $this->parent->getPrimaryKey());
        return ($this->childClass)::find($relationshipKey->build());
    }

    public function save(IElasticsearchModel $child)
    {
        $relationshipKey = new RelationshipKey($child->getPrimaryKey(), $this->parent->getPrimaryKey());
        $child->setPrimaryKey($relationshipKey->build());
        $child->save();
    }
}
