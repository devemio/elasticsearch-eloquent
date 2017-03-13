<?php

namespace Isswp101\Persimmon\Relationship;

use Isswp101\Persimmon\Collection\IElasticsearchCollection;
use Isswp101\Persimmon\CollectionParser\ElasticsearchCollectionParser;
use Isswp101\Persimmon\Model\IElasticsearchModel;
use Isswp101\Persimmon\QueryBuilder\QueryBuilder;

class HasManyRelationship
{
    protected $parent;

    /**
     * @var IElasticsearchModel
     */
    protected $childClass;

    public function __construct(IElasticsearchModel $parent, string $childClass)
    {
        $this->parent = $parent;
        $this->childClass = $childClass;
    }

    public function get(): IElasticsearchCollection
    {
        $collection = new ElasticsearchCollectionParser(($this->childClass)::getCollection());
        $query = [
            'query' => [
                'parent_id' => [
                    'type' => $collection->getType(),
                    'id' => $this->parent->getPrimaryKey()
                ]
            ]
        ];
        return ($this->childClass)::all(new QueryBuilder($query));
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
