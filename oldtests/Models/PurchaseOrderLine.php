<?php

namespace Isswp101\Persimmon\Test\Models;

class PurchaseOrderLine extends ElasticsearchModel
{
    public static $_index = 'test_parent_child_rel';
    public static $_type = 'lines';
    public static $_parentType = 'orders';

    public $name;

    public function po()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
}
