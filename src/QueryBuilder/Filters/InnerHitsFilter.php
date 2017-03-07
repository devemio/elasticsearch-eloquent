<?php

namespace Isswp101\Persimmon\QueryBuilder\Filters;

class InnerHitsFilter extends Filter
{
    protected $parentType;
    protected $fields = [];

    public function __construct($parentType, array $fields = [])
    {
        parent::__construct();

        $this->parentType = $parentType;
        $this->fields = $fields;
    }

    public function query($values)
    {
        $query = [
            'has_parent' => [
                'type' => $this->parentType,
                'filter' => [
                    'match_all' => new \stdClass()
                ],
                'inner_hits' => [
                    '_source' => ['includes' => $this->fields]
                ]
            ]
        ];
        return $query;
    }
}
