<?php

namespace Isswp101\Persimmon\Tests;

use Isswp101\Persimmon\Queries\MatchAllQuery;
use Isswp101\Persimmon\QueryBuilder\QueryBuilder;
use Isswp101\Persimmon\Tests\Models\Product;
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
        Product::create(['id' => rand(1, 100), 'price' => rand(1, 1000)]);
        Product::create(['id' => rand(1, 100), 'price' => rand(1, 1000)]);
        Product::create(['id' => rand(1, 100), 'price' => rand(1, 1000)]);
        Product::create(['id' => rand(1, 100), 'price' => rand(1, 1000)]);
        Product::create(['id' => rand(1, 100), 'price' => rand(1, 1000)]);
        Product::create(['id' => rand(1, 100), 'price' => rand(1, 1000)]);
        Product::create(['id' => rand(1, 100), 'price' => rand(1, 1000)]);
        Product::create(['id' => rand(1, 100), 'price' => rand(1, 1000)]);
        Product::create(['id' => rand(1, 100), 'price' => rand(1, 1000)]);
        Product::create(['id' => rand(1, 100), 'price' => rand(1, 1000)]);
        Product::create(['id' => rand(1, 100), 'price' => rand(1, 1000)]);
        Product::create(['id' => rand(1, 100), 'price' => rand(1, 1000)]);
        Product::create(['id' => rand(1, 100), 'price' => rand(1, 1000)]);

        $query = [
            'size' => 60
        ];

        $products = Product::search($query);

        dd(count($products));

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
