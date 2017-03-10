<?php

namespace Isswp101\Persimmon\Repository;

use Elasticsearch\Client;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Isswp101\Persimmon\Collection\ElasticsearchCollection;
use Isswp101\Persimmon\Collection\IElasticsearchCollection;
use Isswp101\Persimmon\Contracts\Storable;
use Isswp101\Persimmon\Exceptions\ClassTypeErrorException;
use Isswp101\Persimmon\Exceptions\ModelNotFoundException;
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

    public function instantiate(string $class): Storable
    {
        $instance = new $class;
        if (!$instance instanceof IElasticsearchModel) {
            throw new ClassTypeErrorException(IElasticsearchModel::class);
        }
        return $instance;
    }

    protected function fill(Storable $model, ElasticsearchResponse $response)
    {
        $model->fill($response->source());
        $model->setPrimaryKey($response->id());
    }

    public function find($id, string $class, array $columns = []): Storable
    {
        $model = $this->instantiate($class);
        $collection = new ElasticsearchCollectionParser($model->getCollectionName());
        $params = [
            'index' => $collection->getIndex(),
            'type' => $collection->getType(),
            'id' => $id,
            '_source' => $columns == ElasticsearchRepository::SOURCE_FALSE ? false : $columns
        ];
        try {
            $response = new ElasticsearchResponse($this->client->get($params));
        } catch (Missing404Exception $e) {
            throw new ModelNotFoundException($class, $id);
        }
        $this->fill($model, $response);
        return $model;
    }

    public function all(
        IQueryBuilder $query,
        string $class,
        array $columns = [],
        callable $callback = null
    ): IElasticsearchCollection {
        $model = $this->instantiate($class);
        $collection = new ElasticsearchCollectionParser($model->getCollectionName());
        $params = [
            'index' => $collection->getIndex(),
            'type' => $collection->getType(),
            'body' => $query->build(),
            '_source' => $columns == ElasticsearchRepository::SOURCE_FALSE ? false : $columns
        ];
        $response = new ElasticsearchResponse($this->client->search($params));
        $models = new ElasticsearchCollection();
        foreach ($response->hits() as $hit) {
            $model = $this->instantiate($class);
            $this->fill($model, new ElasticsearchResponse($hit));
            if ($callback != null) {
                $callback($model);
            }
            $models->put($model->getPrimaryKey(), $model);
        }
        return $models;
    }

    public function insert(Storable $model)
    {
        $collection = new ElasticsearchCollectionParser($model->getCollectionName());
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
        $collection = new ElasticsearchCollectionParser($model->getCollectionName());
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
        $collection = new ElasticsearchCollectionParser($model->getCollectionName());
        $params = [
            'index' => $collection->getIndex(),
            'type' => $collection->getType(),
            'id' => $model->getPrimaryKey()
        ];
        $this->client->delete($params);
    }
}