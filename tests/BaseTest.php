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
        Product::create(['id' => 2, 'price' => 10]);

        $products = Product::search([
            'from' => 1,
            'query' => [
                'match_all' => new \stdClass()
            ]
        ]);

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
