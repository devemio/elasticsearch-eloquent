<?php

namespace Isswp101\Persimmon\Model;

use Isswp101\Persimmon\Collection\IElasticsearchCollection;
use Isswp101\Persimmon\DI\Container;
use Isswp101\Persimmon\DI\DI;
use Isswp101\Persimmon\QueryBuilder\IQueryBuilder;
use Isswp101\Persimmon\Relationship\BelongsToRelationship;
use Isswp101\Persimmon\Relationship\HasManyRelationship;
use Isswp101\Persimmon\Relationship\RelationshipKey;

/**
 * @method static static find($id, array $columns = [])
 * @method static IElasticsearchCollection all(IQueryBuilder $query, callable $callback = null)
 */
class ElasticsearchModel extends Eloquent implements IElasticsearchModel
{
    protected $relationshipKey;

    protected static function di(): Container
    {
        return DI::make(DI::ELASTICSEARCH);
    }

    public function getRelationshipKey(): RelationshipKey
    {
        return $this->relationshipKey;
    }

    public function setRelationshipKey(RelationshipKey $relationshipKey)
    {
        $this->relationshipKey = $relationshipKey;
    }

    protected function belongsTo(string $class)
    {
        return new BelongsToRelationship($this, $class);
    }

    protected function hasMany(string $class)
    {
        return new HasManyRelationship($this, $class);
    }
}