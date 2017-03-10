<?php

namespace Isswp101\Persimmon\Collection;

use Isswp101\Persimmon\Response\ElasticsearchCollectionResponse;

class ElasticsearchCollection extends Collection implements IElasticsearchCollection
{
    private $response;

    public function __construct(array $items = [], ElasticsearchCollectionResponse $response)
    {
        parent::__construct($items);
        $this->response = $response;
    }

    public function getElasticsearchResponse(): ElasticsearchCollectionResponse
    {
        return $this->response;
    }
}