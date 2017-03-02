<?php

namespace Isswp101\Persimmon\Response;

class ElasticsearchResponse
{
    private $response;

    public function __construct(array $response)
    {
        $this->response = $response;
    }

    public function isFound(): bool
    {
        return $this->response['found'] ?? null;
    }

    public function source(): array
    {
        return $this->response['_source'] ?? [];
    }

    public function getHits(): array
    {
        return $this->response['hits']['hits'] ?? [];
    }
}