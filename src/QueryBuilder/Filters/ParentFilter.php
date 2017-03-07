<?php

namespace Isswp101\Persimmon\QueryBuilder\Filters;

class ParentFilter extends Filter
{
    protected $type;

    public function __construct($id, $type)
    {
        $this->type = $type;

        parent::__construct($id);
    }

    public function query($values)
    {
        $query = [
            'parent_id' => [
                'type' => $this->type,
                'id' => $values
            ]
        ];
        return $query;
    }
}
