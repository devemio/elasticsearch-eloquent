<?php

namespace Isswp101\Persimmon\Response;

class ElasticsearchResponse
{
    private $response;

    public function __construct(array $response)
    {
        $this->response = $response;
    }

    public function found(): bool
    {
        return $this->response['found'] ?? null;
    }

    public function created(): bool
    {
        return $this->response['created'] ?? null;
    }

    public function source(): array
    {
        return $this->response['_source'] ?? [];
    }

    public function hits(): array
    {
        return $this->response['hits']['hits'] ?? [];
    }
}