<?php

namespace Isswp101\Persimmon\Model;

use Elasticsearch\ClientBuilder;
use Isswp101\Persimmon\Collection\IElasticsearchCollection;
use Isswp101\Persimmon\DI\Container;
use Isswp101\Persimmon\DI\DI;
use Isswp101\Persimmon\QueryBuilder\IQueryBuilder;
use Isswp101\Persimmon\Relationship\BelongsToRelationship;
use Isswp101\Persimmon\Relationship\HasManyRelationship;
use Isswp101\Persimmon\Repository\ElasticsearchRepository;
use Isswp101\Persimmon\Repository\RuntimeCacheRepository;

/**
 * @method static static|null find(string $id, array $columns = [])
 * @method static IElasticsearchCollection all(IQueryBuilder $query, callable $callback = null)
 */
class ElasticsearchModel extends Eloquent implements IElasticsearchModel
{
    protected static function di(): Container
    {
        $container = DI::make('elasticsearch');
        if ($container == null) {
            DI::bind('elasticsearch', function () {
                $client = ClientBuilder::create()->build();
                return new Container(new ElasticsearchRepository($client), new RuntimeCacheRepository());
            }, DI::SINGLETON);
            $container = DI::make('elasticsearch');
        }
        return $container;
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