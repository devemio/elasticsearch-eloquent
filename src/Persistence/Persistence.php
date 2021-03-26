<?php

namespace Isswp101\Persimmon\Persistence;

use Elasticsearch\Client;
use Isswp101\Persimmon\Contracts\PersistenceContract;
use Isswp101\Persimmon\DTO\Path;

final class Persistence implements PersistenceContract
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function find(Path $path): array
    {
        return [
            'id' => 1,
            'price' => 10
        ];
    }

    public function save(Path $path, array $attributes): mixed
    {
        return 1;
    }

    public function delete(Path $path): void
    {

    }
}
