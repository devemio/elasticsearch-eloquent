<?php

namespace Isswp101\Persimmon\QueryBuilder\Aggregations;

class TermsAggregation extends Aggregation
{
    protected $field;
    protected $size = 0;

    public function __construct($field, $size = 0)
    {
        parent::__construct($field);
        $this->field = $field;
        $this->size = $size;
    }

    public function make()
    {
        return [
            'terms' => [
                'field' => $this->field,
                // 'size' => $this->size
            ]
        ];
    }
}
