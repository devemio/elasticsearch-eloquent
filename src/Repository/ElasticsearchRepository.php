<?php

namespace Isswp101\Persimmon\Repository;

use Elasticsearch\Client;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Isswp101\Persimmon\Collection\ElasticsearchCollection;
use Isswp101\Persimmon\Collection\ICollection;
use Isswp101\Persimmon\CollectionParser\ElasticsearchCollectionParser;
use Isswp101\Persimmon\Contracts\Storable;
use Isswp101\Persimmon\Exceptions\ClassTypeErrorException;
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
        if (!$instance instanceof Storable) {
            throw new ClassTypeErrorException(Storable::class);
        }
        return $instance;
    }

    protected function fill(Storable $model, ElasticsearchItemResponse $response)
    {
        $relationshipKey = new RelationshipKey($response->id(), $response->parent());
        $model->setPrimaryKey($relationshipKey->build());
        $model->fill($response->source());
    }

    public function find(string $id, string $class, array $columns = []): ?Storable
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
            return null;
        }
        $this->fill($model, $response);
        return $model;
    }

    public function all(IQueryBuilder $query, string $class, callable $callback = null): ICollection
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
            $this->fill($model, new ElasticsearchItemResponse($hit));
            if ($callback != null) {
                $callback($model);
            }
            $models->put($model->getPrimaryKey(), $model);
        }
        return $models;
    }

    public function insert(Storable $model): void
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

    public function update(Storable $model): void
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

    public function delete(Storable $model): void
    {
        $relationshipKey = RelationshipKey::parse($model->getPrimaryKey());
        $collection = new ElasticsearchCollectionParser($model->getCollection());
        $params = [
            'index' => $collection->getIndex(),
            'type' => $collection->getType(),
            'id' => $relationshipKey->getId(),
            'parent' => $relationshipKey->getParentId()
        ];
        $this->client->delete($params);
    }
}