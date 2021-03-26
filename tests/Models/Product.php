<?php

namespace Isswp101\Persimmon\Tests\Models;

use Isswp101\Persimmon\Models\BaseElasticsearchModel;

class Product extends BaseElasticsearchModel
{
    protected string $index = 'index';
    protected string $type = 'type';
}
