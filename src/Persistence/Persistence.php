<?php

namespace Isswp101\Persimmon\Persistence;

use Elasticsearch\Client;
use Exception;
use Isswp101\Persimmon\Contracts\PersistenceContract;
use Isswp101\Persimmon\DTO\Id;
use Isswp101\Persimmon\DTO\Path;
use Isswp101\Persimmon\DTO\SearchResponse;

final class Persistence implements PersistenceContract
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function find(Path $path, array $columns = []): array|null
    {
        $params = [
            'index' => $path->getIndex(),
            'id' => $path->getId()->value()
        ];

        if ($columns) {
            $params['_source'] = $columns;
        }

        try {
            $response = $this->client->get($params);
        } catch (Exception) {
            return null;
        }

        return $response['_source'];
    }

    public function save(Path $path, array $attributes): Id
    {
        $params = [
            'index' => $path->getIndex(),
            'id' => $path->getId()->value(),
            'body' => $attributes
        ];

        $response = $this->client->index($params);

        return new Id($response['_id']);
    }

    public function delete(Path $path): void
    {
        $params = [
            'index' => $path->getIndex(),
            'id' => $path->getId()->value(),
        ];

        $this->client->delete($params);
    }

    public function search(Path $path, array $query): SearchResponse
    {
        $params = [
            'index' => $path->getIndex(),
            'body' => $query
        ];

        return new SearchResponse($this->client->search($params));
    }
}
