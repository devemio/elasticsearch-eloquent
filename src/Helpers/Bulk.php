<?php

namespace Isswp101\Persimmon\Helpers;

use Elasticsearch\Client;
use Exception;
use Isswp101\Persimmon\CollectionParser\ElasticsearchCollectionParser;
use Isswp101\Persimmon\Model\IElasticsearchModel;

class Bulk
{
    /**
     * @param Client $client
     * @param IElasticsearchModel[] $models
     * @param int $chunk
     */
    public static function index(Client $client, array $models, int $chunk = 1000): void
    {
        $i = 1;
        $params = ['body' => []];
        foreach ($models as $model) {
            try {
                $id = $model->getPrimaryKey();
            } catch (Exception $e) {
                $id = $i++;
            }
            $collection = new ElasticsearchCollectionParser($model::getCollection());
            $params['body'][] = [
                'index' => [
                    '_index' => $collection->getIndex(),
                    '_type' => $collection->getType(),
                    '_id' => $id
                ]
            ];
            $params['body'][] = $model->toArray();
            if ($i % $chunk == 0) {
                $responses = $client->bulk($params);
                $params = ['body' => []];
                unset($responses);
            }
        }
        if (!empty($params['body'])) {
            $client->bulk($params);
        }
    }
}