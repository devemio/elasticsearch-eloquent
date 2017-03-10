<?php

namespace Isswp101\Persimmon\Model;

use Isswp101\Persimmon\Collection\IElasticsearchCollection;
use Isswp101\Persimmon\DI\Container;
use Isswp101\Persimmon\DI\DI;
use Isswp101\Persimmon\QueryBuilder\IQueryBuilder;

/**
 * @method static IElasticsearchCollection all(IQueryBuilder $query, array $columns = [], callable $callback = null)
 */
class ElasticsearchModel extends Eloquent implements IElasticsearchModel
{
    protected static function di(): Container
    {
        return DI::make(DI::ELASTICSEARCH);
    }
}