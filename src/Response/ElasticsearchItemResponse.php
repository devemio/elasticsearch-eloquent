<?php

namespace Isswp101\Persimmon\Response;

class ElasticsearchItemResponse
{
    private $response;

    public function __construct(array $response)
    {
        $this->response = $response;
    }

    public function index(): string
    {
        return $this->response['_index'];
    }

    public function type(): string
    {
        return $this->response['_type'];
    }

    public function id(): string
    {
        return $this->response['_id'];
    }

    public function parent()
    {
        return $this->response['_parent'];
    }

    public function score(): int
    {
        return $this->response['_score'] ?? 0;
    }

    public function found(): bool
    {
        return $this->response['found'] ?? false;
    }

    public function created(): bool
    {
        return $this->response['created'] ?? false;
    }

    public function source(): array
    {
        return $this->response['_source'] ?? [];
    }
}