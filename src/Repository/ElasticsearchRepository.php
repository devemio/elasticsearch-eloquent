<?php

namespace Isswp101\Persimmon\Repository;

use Elasticsearch\Client;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Isswp101\Persimmon\Collection\ElasticsearchCollection;
use Isswp101\Persimmon\Collection\IElasticsearchCollection;
use Isswp101\Persimmon\CollectionParser\ElasticsearchCollectionParser;
use Isswp101\Persimmon\Contracts\Storable;
use Isswp101\Persimmon\Exceptions\ClassTypeErrorException;
use Isswp101\Persimmon\Exceptions\ModelNotFoundException;
use Isswp101\Persimmon\Model\IElasticsearchModel;
use Isswp101\Persimmon\QueryBuilder\IQueryBuilder;
use Isswp101\Persimmon\Relationship\RelationshipKey;
use Isswp101\Persimmon\Response\ElasticsearchCollectionResponse;
use Isswp101\Persimmon\Response\ElasticsearchItemResponse;

class ElasticsearchRepository implements IRepository
{
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

    protected function fill(
        Storable $model,
        ElasticsearchItemResponse $response,
        RelationshipKey $relationshipKey = null
    ) {
        $model->fill($response->source());
        $model->setPrimaryKey($relationshipKey != null ? $relationshipKey->build() : $response->id());
    }

    public function find($id, string $class, array $columns = []): Storable
    {
        $model = $this->instantiate($class);
        $relationshipKey = RelationshipKey::parse($id);
        $collection = new ElasticsearchCollectionParser($model->getCollection());
        $params = [
            'index' => $collection->getIndex(),
            'type' => $collection->getType(),
            'id' => $relationshipKey->getId(),
            'parent' => $relationshipKey->getParentId(),
            '_source' => $columns
        ];
        try {
            $response = new ElasticsearchItemResponse($this->client->get($params));
        } catch (Missing404Exception $e) {
            throw new ModelNotFoundException($class, $id);
        }
        $this->fill($model, $response, $relationshipKey);
        return $model;
    }

    public function all(IQueryBuilder $query, string $class, callable $callback = null): IElasticsearchCollection
    {
        $model = $this->instantiate($class);
        $collection = new ElasticsearchCollectionParser($model->getCollection());
        $params = [
            'index' => $collection->getIndex(),
            'type' => $collection->getType(),
            'body' => $query->build()
        ];
        $response = new ElasticsearchCollectionResponse($this->client->search($params));
        $models = new ElasticsearchCollection([], $response);
        foreach ($response->hits() as $hit) {
            $model = $this->instantiate($class);
            $item = new ElasticsearchItemResponse($hit);
            $this->fill($model, $item/*, new RelationshipKey($item->id())*/);
            if ($callback != null) {
                $callback($model);
            }
            $models->put($model->getPrimaryKey(), $model);
        }
        return $models;
    }

    public function insert(Storable $model)
    {
        $relationshipKey = RelationshipKey::parse($model->getPrimaryKey());
        $collection = new ElasticsearchCollectionParser($model->getCollection());
        $params = [
            'index' => $collection->getIndex(),
            'type' => $collection->getType(),
            'id' => $relationshipKey->getId(),
            'parent' => $relationshipKey->getParentId(),
            'body' => $model->toArray()
        ];
        $this->client->index($params);
    }

    public function update(Storable $model)
    {
        $relationshipKey = RelationshipKey::parse($model->getPrimaryKey());
        $collection = new ElasticsearchCollectionParser($model->getCollection());
        $params = [
            'index' => $collection->getIndex(),
            'type' => $collection->getType(),
            'id' => $relationshipKey->getId(),
            'parent' => $relationshipKey->getParentId(),
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