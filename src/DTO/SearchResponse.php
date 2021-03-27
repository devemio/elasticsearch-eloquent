<?php

namespace Isswp101\Persimmon\DTO;

final class SearchResponse
{
    private int $total;
    private array $items = [];

    public function __construct(array $response)
    {
        $this->total = $response['hits']['total']['value'];

        foreach ($response['hits']['hits'] as $hit) {
            $this->items[] = $hit['_source'];
        }
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getItems(): array
    {
        return $this->items;
    }
}
