<?php

namespace Isswp101\Persimmon\Models;

use Isswp101\Persimmon\Model\ElasticsearchModel;

class Employee extends ElasticsearchModel
{
    const COLLECTION = 'company/employee';

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}