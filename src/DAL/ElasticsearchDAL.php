<?php

namespace Isswp101\Persimmon\DAL;

use Elasticsearch\Client;
use Illuminate\Support\Arr;
use Isswp101\Persimmon\Collection\ElasticsearchCollection;
use Isswp101\Persimmon\ElasticsearchModel;
use Isswp101\Persimmon\Event\EventEmitter;
use Isswp101\Persimmon\Model;

class ElasticsearchDAL implements IDAL
{
    protected $model;
    protected $client;
    protected $emitter;

    public function __construct(ElasticsearchModel $model, Client $client, EventEmitter $emitter)
    {
        $this->model = $model;
        $this->client = $client;
        $this->emitter = $emitter;
    }

    public function setModel(Model $model)
    {
        $this->model = $model;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function getEventEmitter()
    {
        return $this->emitter;
    }

    public function get($id, array $options = [])
    {
        $params = $this->model->getPath()->toArray();

        if (!empty($options['columns'])) {
            $params['_source'] = $options['columns'];
        }

        if (!empty($options['parent'])) {
            $params['parent'] = $options['parent'];
        }

        $response = $this->client->get($params);

        $this->model->fillByResponse($response);

        if (isset($params['parent'])) {
            $this->model->setParentId($params['parent']);
        }

        return $this->model;
    }

    public function put(array $columns = ['*'])
    {
        $params = $this->getParams();

        if (!$this->model->_exist || $columns == ['*']) {
            if (!$params['id']) {
                unset($params['id']);
            }

            $params['body'] = $this->model->toArray();

            $response = $this->client->index($params);
        } else {
            $params['body'] = [
                'doc' => array_only($this->model->toArray(), $columns)
            ];

            $response = $this->client->update($params);
        }

        $this->model->setId($response['_id']);

        return $this->model->getId();
    }

    public function delete()
    {
        return $this->client->delete($this->getParams());
    }

    protected function getParams()
    {
        $params = $this->model->getPath()->toArray();

        if ($this->model->getParentId()) {
            $params['parent'] = $this->model->getParentId();
        }

        return $params;
    }

    public function search(array $query)
    {
        if (empty($query['body']['query']) && empty($query['body']['filter'])) {
            $query['body']['query'] = [
                'match_all' => new \stdClass()
            ];
        }

        $params = [
            'index' => $this->model->getIndex(),
            'type' => $this->model->getType(),
            'from' => Arr::get($query, 'from', 0),
            'size' => Arr::get($query, 'size', 50),
            'body' => $query['body']
        ];

        $collection = new ElasticsearchCollection();

        $this->emitter->trigger(DALEvents::BEFORE_SEARCH, $params);

        $response = $this->client->search($params);

        $this->emitter->trigger(DALEvents::AFTER_SEARCH, $response);

        $collection->response($response);

        $from = (int)$params['from'];
        foreach ($response['hits']['hits'] as $hit) {
            $model = $this->model->createInstance();
            $model->_score = $hit['_score'];
            $model->_position = $from++;
            $model->_exist = true;
            $model->fillByResponse($hit);
            $model->fillByInnerHits($hit);
            $collection->put($model->getId(), $model);
        }

        return $collection;
    }
}
