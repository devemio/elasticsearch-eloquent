<?php

namespace Isswp101\Persimmon\Collection;

use Isswp101\Persimmon\Response\ElasticsearchCollectionResponse;

interface IElasticsearchCollection extends ICollection
{
    public function getElasticsearchResponse(): ElasticsearchCollectionResponse;
}