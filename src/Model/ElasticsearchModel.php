<?php

namespace Isswp101\Persimmon\Model;

use Isswp101\Persimmon\Collection\IElasticsearchCollection;
use Isswp101\Persimmon\DI\Container;
use Isswp101\Persimmon\DI\DI;
use Isswp101\Persimmon\QueryBuilder\IQueryBuilder;
use Isswp101\Persimmon\Relationship\BelongsToRelationship;
use Isswp101\Persimmon\Relationship\HasManyRelationship;

/**
 * @method static static|null find(string $id, array $columns = [])
 * @method static IElasticsearchCollection all(IQueryBuilder $query, callable $callback = null)
 */
class ElasticsearchModel extends Eloquent implements IElasticsearchModel
{
    protected static function di(): Container
    {
        return DI::make(DI::ELASTICSEARCH);
    }

    protected function belongsTo(string $class): BelongsToRelationship
    {
        return new BelongsToRelationship($this, $class);
    }

    protected function hasMany(string $class): HasManyRelationship
    {
        return new HasManyRelationship($this, $class);
    }
}