<?php

namespace Isswp101\Persimmon\Test\Models;

class PurchaseOrder extends ElasticsearchModel
{
    public static $_index = 'test_parent_child_rel';
    public static $_type = 'orders';

    public $name;

    public function lines()
    {
        return $this->hasMany(PurchaseOrderLine::class);
    }
}
