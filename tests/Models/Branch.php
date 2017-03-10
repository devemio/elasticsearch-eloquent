<?php

namespace Isswp101\Persimmon\Models;

use Isswp101\Persimmon\Model\ElasticsearchModel;

class Branch extends ElasticsearchModel
{
    const COLLECTION = 'company/branch';

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}