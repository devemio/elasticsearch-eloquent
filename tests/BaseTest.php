<?php

namespace Isswp101\Persimmon\Tests;

use Isswp101\Persimmon\Tests\Models\Product;
use ONGR\ElasticsearchDSL\Query\MatchAllQuery;
use ONGR\ElasticsearchDSL\Search;
use PHPUnit\Framework\TestCase;

function dd($value): void
{
    print_r($value);
    exit();
}

class BaseTest extends TestCase
{
    public function testTrue(): void
    {
        Product::create(['id' => 1, 'price' => 10]);

        $matchAll = new MatchAllQuery();
        $search = new Search();
        $search->addQuery($matchAll);

        $products = Product::search($search->toArray());

        dd($products[0]->toArray());

//        $product = new Product([
//            'price' => 10
//        ]);
//
//        $product->save();
//
//        dd($product->toArray());

        $this->assertTrue(true);
    }
}
