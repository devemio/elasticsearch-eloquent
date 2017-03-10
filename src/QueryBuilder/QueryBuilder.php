<?php

namespace Isswp101\Persimmon\QueryBuilder;

use Isswp101\Persimmon\Contracts\Stringable;
use ONGR\ElasticsearchDSL\Search;

class QueryBuilder implements IQueryBuilder, Stringable
{
    protected $query = [];
    protected $chunk = 0;

    /**
     * @param Search|array|null $query
     */
    public function __construct($query = null)
    {
        if ($query instanceof Search) {
            $query = $query->toArray();
        }
        $this->query = is_array($query) ? $query : $this->getMatchAllQuery();
    }

    protected function getMatchAllQuery()
    {
        return ['query' => ['match_all' => new \stdClass()]];
    }

    public function build(): array
    {
        return $this->query;
    }

    public function setChunkCount(int $count): IQueryBuilder
    {
        $this->chunk = $count;
        return $this;
    }

    public function getChunkCount(): int
    {
        return $this->chunk;
    }

    public function __toString(): string
    {
        return json_encode($this->query);
    }
}
