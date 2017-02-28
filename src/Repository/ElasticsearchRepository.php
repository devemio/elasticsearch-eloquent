<?php

namespace Isswp101\Persimmon\Repository;

use Elasticsearch\Client;

class ElasticsearchRepository
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }
}