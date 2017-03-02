<?php

namespace Isswp101\Persimmon\Test\Models;

class Product extends ElasticsearchModel
{
    public static $_index = 'test';
    public static $_type = 'test';

    public $name;
    public $price = 0;
}
