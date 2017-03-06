<?php

namespace Isswp101\Persimmon\Repository;

use Elasticsearch\Client;
use Isswp101\Persimmon\Collection\ICollection;
use Isswp101\Persimmon\Contracts\Storable;
use Isswp101\Persimmon\Exceptions\ClassTypeErrorException;
use Isswp101\Persimmon\Model\IElasticsearchModel;
use Isswp101\Persimmon\QueryBuilder\IQueryBuilder;
use Isswp101\Persimmon\Response\ElasticsearchResponse;

class ElasticsearchRepository implements IRepository
{
    const SOURCE_FALSE = [false];

    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    protected function instantiate(string $class): Storable
    {
        $instance = new $class;
        if (!$instance instanceof IElasticsearchModel) {
            throw new ClassTypeErrorException(IElasticsearchModel::class);
        }
        return $instance;
    }

    public function find($id, string $class, array $columns = []): Storable
    {
        $model = $this->instantiate($class);
        $collection = new ElasticsearchCollectionParser($model->getCollection());
        $params = [
            'index' => $collection->getIndex(),
            'type' => $collection->getType(),
            'id' => $id,
            '_source' => $columns == ElasticsearchRepository::SOURCE_FALSE ? false : $columns
        ];
        $response = new ElasticsearchResponse($this->client->get($params));
        $model->fill($response->source());
        return $model;
    }

    public function all(
        IQueryBuilder $query = null,
        string $class,
        array $columns = [],
        callable $callback = null
    ): ICollection {
        $models = [];
        $model = $this->instantiate($class);
        $collection = new ElasticsearchCollectionParser($model->getCollection());
        $params = [
            'index' => $collection->getIndex(),
            'type' => $collection->getType(),
            'body' => $query->build(),
            '_source' => $columns == ElasticsearchRepository::SOURCE_FALSE ? false : $columns
        ];
        $response = new ElasticsearchResponse($this->client->search($params));
        foreach ($response->hits() as $hit) {
            $hit = new ElasticsearchResponse($hit);
            $model = $this->instantiate($class);
            $model->fill($hit->source());
            if ($callback != null) {
                $callback($model);
            }
            $models[] = $model;
        }
        return $models;
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
        $this->client->index($params);
    }

    public function update(Storable $model)
    {
        $collection = new ElasticsearchCollectionParser($model->getCollection());
        $params = [
            'index' => $collection->getIndex(),
            'type' => $collection->getType(),
            'id' => $model->getPrimaryKey(),
            'body' => $model->toArray()
        ];
        $this->client->index($params);
    }

    public function delete(Storable $model)
    {
        $collection = new ElasticsearchCollectionParser($model->getCollection());
        $params = [
            'index' => $collection->getIndex(),
            'type' => $collection->getType(),
            'id' => $model->getPrimaryKey()
        ];
        $this->client->delete($params);
    }
}