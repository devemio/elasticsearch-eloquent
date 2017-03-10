<?php

namespace Isswp101\Persimmon\QueryBuilder;

use Isswp101\Persimmon\QueryBuilder\Aggregations\Aggregation;
use Isswp101\Persimmon\QueryBuilder\Filters\Filter;
use ONGR\ElasticsearchDSL\Search;

class QueryBuilder implements IQueryBuilder
{
    protected $query = [];

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

    public function from(int $from): self
    {
        $this->query['from'] = $from;
        return $this;
    }

    public function size(int $size): self
    {
        $this->query['size'] = $size;
        return $this;
    }

    public function hasSort(): bool
    {
        return !empty($this->query['body']['sort']);
    }

    /**
     * @param array|string $sort
     * @return $this
     */
    public function sort($sort)
    {
        $this->query['body']['sort'] = $sort;
        return $this;
    }

    /**
     * @param Filter|array $filter
     * @param string $mode
     * @return $this
     */
    public function filter($filter = [], $mode = Filter::MODE_INCLUDE)
    {
        if (!$filter) {
            unset($this->query['body']['filter']);
            return $this;
        }

        if ($filter instanceof Filter) {
            $filter = $filter->makeQuery();
        }

        $map = [
            Filter::MODE_INCLUDE => 'must',
            Filter::MODE_EXCLUDE => 'must_not',
            'should' => 'should'
        ];

        $mode = $map[$mode];

        $this->merge($filter, $mode);

        return $this;
    }

    /**
     * Set _source to search query.
     *
     * @param mixed $fields
     * @return $this
     */
    public function fields($fields = false)
    {
        if ($fields === false) {
            $this->query['body']['_source'] = false;
        } elseif ((array)$fields == ['*']) {
            unset($this->query['body']['_source']);
        } else {
            $this->query['body']['_source'] = $fields;
        }
        return $this;
    }

    /**
     * Return only _id.
     *
     * @return $this
     */
    public function getOnlyIds()
    {
        return $this->fields(false);
    }

    public function build(): array
    {
        return $this->query;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * @param int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->query, $options);
    }

    protected function merge(array $query, $mode = 'must')
    {
        if (!array_key_exists('filter', $this->query['body'])) {
            $this->query['body']['filter']['bool'][$mode] = [];
        }

        $this->query['body']['filter']['bool'][$mode][] = $query;

        return $this;
    }

    public function aggregation(Aggregation $aggregation)
    {
        $this->query['body']['aggs'][$aggregation->getName()] = $aggregation->make();
        return $this;
    }

    public function where($field, $value)
    {
        $query = ['term' => [$field => $value]];
        $this->merge($query);
        return $this;
    }

    public function notWhere($field, $value)
    {
        $query = ['term' => [$field => $value]];
        $this->merge($query, 'must_not');
        return $this;
    }

    public function orWhere($field, $value)
    {
        $query = ['term' => [$field => $value]];
        $this->merge($query, 'should');
        return $this;
    }

    public function between($field, $start, $end)
    {
        $query = ['range' => [$field => ['gt' => $start, 'lt' => $end]]];
        $this->merge($query);
        return $this;
    }

    public function betweenOrEquals($field, $start, $end)
    {
        $query = ['range' => [$field => ['gte' => $start, 'lte' => $end]]];
        $this->merge($query);
        return $this;
    }

    public function range($field, $start, $end)
    {
        return $this->betweenOrEquals($field, $start, $end);
    }

    public function greaterThan($field, $start)
    {
        $query = ['range' => [$field => ['gt' => $start]]];
        $this->merge($query);
        return $this;
    }

    public function gt($field, $start)
    {
        return $this->greaterThan($field, $start);
    }

    public function greaterThanOrEquals($field, $start)
    {
        $query = ['range' => [$field => ['gte' => $start]]];
        $this->merge($query);
        return $this;
    }

    public function gte($field, $start)
    {
        return $this->greaterThanOrEquals($field, $start);
    }

    public function lessThan($field, $end)
    {
        $query = ['range' => [$field => ['lt' => $end]]];
        $this->merge($query);
        return $this;
    }

    public function lt($field, $start)
    {
        return $this->lessThan($field, $start);
    }

    public function lessThanOrEquals($field, $end)
    {
        $query = ['range' => [$field => ['lte' => $end]]];
        $this->merge($query);
        return $this;
    }

    public function lte($field, $start)
    {
        return $this->lessThanOrEquals($field, $start);
    }

    public function match($field, $value)
    {
        $query = ['query' => ['match' => [$field => $value]]];
        $this->merge($query);
        return $this;
    }

    public function notMatch($field, $value)
    {
        $query = ['query' => ['match' => [$field => $value]]];
        $this->merge($query, 'must_not');
        return $this;
    }

    public function orMatch($field, $value)
    {
        $query = ['query' => ['match' => [$field => $value]]];
        $this->merge($query, 'should');
        return $this;
    }
}
