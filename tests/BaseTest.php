<?php

namespace Isswp101\Persimmon\Tests;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Isswp101\Persimmon\Queries\MatchAllQuery;
use Isswp101\Persimmon\Tests\Models\Product;
use PHPUnit\Framework\TestCase;

function dd($value): void
{
    print_r($value);
    echo PHP_EOL;
    exit();
}

class BaseTest extends TestCase
{
    private Client $client;

    private array $attributes = [
        'name' => 'Name',
        'price' => 10
    ];

    protected function setUp(): void
    {
        $this->client = ClientBuilder::create()->build();
    }

    public function testFillModel(): void
    {
        $a = new Product($this->attributes);

        $b = new Product();
        $b->fill($this->attributes);

        $this->assertEquals($this->attributes, $a->toArray());
        $this->assertEquals($this->attributes, $b->toArray());
    }

    public function testCreateModel(): void
    {
        $product = Product::create($this->attributes);

        $this->assertNotNull($product->getId());

        $params = [
            'index' => $product->getIndex(),
            'type' => $product->getType(),
            'id' => $product->getId()
        ];

        $res = $this->client->get($params)['_source'];

        $this->assertEquals($product->name, $res['name']);
        $this->assertEquals($product->price, $res['price']);
    }

    public function testDeleteModel(): void
    {
        $product = Product::create(array_merge(['id' => 1], $this->attributes));

        Product::destroy(1);

        $product->exists();
    }
}
