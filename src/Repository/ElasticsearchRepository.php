<?php

namespace Isswp101\Persimmon\Repository;

use Elasticsearch\Client;
use Isswp101\Persimmon\Collection\ICollection;
use Isswp101\Persimmon\Contracts\Storable;
use Isswp101\Persimmon\Model\IEloquent;
use Isswp101\Persimmon\QueryBuilder\IQueryBuilder;

class ElasticsearchRepository implements IRepository
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function find($id, string $class, array $columns = null): IEloquent
    {
        // TODO: Implement find() method.
    }

    public function all(IQueryBuilder $query, string $class, array $columns = null): ICollection
    {
        // TODO: Implement all() method.
    }

    public function insert(Storable $model)
    {
        $collection = new ElasticsearchCollectionParser($model->getCollection());
        $params = [
            'index' => $collection->getIndex(),
            'type' => $collection->getType(),
            'id' => $model->getPrimaryKey(),
            'body' => $model->toArray()
        ];
        $response = $this->client->index($params);
        // @TODO Validate response
    }

    public function update(Storable $model)
    {
        // TODO: Implement update() method.
    }

    public function delete($id)
    {
        // TODO: Implement delete() method.
    }
}