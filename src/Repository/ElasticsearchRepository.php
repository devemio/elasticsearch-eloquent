<?php

namespace Isswp101\Persimmon\Repository;

use Elasticsearch\Client;
use Isswp101\Persimmon\Collection\ICollection;
use Isswp101\Persimmon\Contracts\Storable;
use Isswp101\Persimmon\Model\IElasticsearchModel;
use Isswp101\Persimmon\QueryBuilder\IQueryBuilder;
use Isswp101\Persimmon\Response\ElasticsearchResponse;

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

    protected function instantiate(string $class): Storable
    {
        return new $class;
    }

    public function find($id, string $class, array $columns = null): Storable
    {
        $model = $this->instantiate($class);
        $collection = new ElasticsearchCollectionParser($model->getCollection());
        $params = [
            'index' => $collection->getIndex(),
            'type' => $collection->getType(),
            'id' => $id
        ];
        $response = new ElasticsearchResponse($this->client->get($params));
        $model->fill($response->source());
        return $model;
    }

    public function all(IQueryBuilder $query = null, string $class, array $columns = null, callable $callback = null): ICollection
    {
        $documents = [];
        $model = $this->instantiate($class);
        $collection = new ElasticsearchCollectionParser($model->getCollection());
        $params = [
            'index' => $collection->getIndex(),
            'type' => $collection->getType(),
            'body' => $query->build()
        ];
        $response = new ElasticsearchResponse($this->client->search($params));
        foreach ($response->getHits() as $hit) {
            $hit = new ElasticsearchResponse($hit);
            $model = $this->instantiate($class);
            $model->fill($hit->source());
            if ($callback != null) {
                $callback($model);
            }
            if ($model instanceof IElasticsearchModel) {
                // ...
            }
            $documents[] = $model;
        }
        return $documents;
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
        $response = new ElasticsearchResponse($this->client->index($params));
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