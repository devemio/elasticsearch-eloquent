<?php

namespace Isswp101\Persimmon\Tests;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Isswp101\Persimmon\Exceptions\ModelNotFoundException;
use Isswp101\Persimmon\Tests\Models\Product;
use PHPUnit\Framework\TestCase;

function dd(mixed $value): void
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
        $this->expectException(Missing404Exception::class);

        $product = Product::create(array_merge(['id' => 1], $this->attributes));

        $this->assertTrue($product->exists());

        $product->delete();

        $this->assertFalse($product->exists());

        Product::destroy(1);
    }

    public function testFindModel(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $product = Product::create($this->attributes);

        $found = Product::find($product->getId());
        $this->assertNotNull($found);
        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals($found->toArray(), $product->toArray());

        $found = Product::find($product->getId(), ['name']);
        $this->assertEquals($found->name, $product->name);
        $this->assertNotEquals($found->toArray(), $product->toArray());

        Product::findOrFail(1000);
    }

    public function testFirstModel(): void
    {
        Product::create($this->attributes);

        $query = [
            'query' => [
                'match' => [
                    'name' => $this->attributes['name']
                ]
            ]
        ];

        $products = Product::search($query);
        $this->assertGreaterThanOrEqual(1, count($products));

        $product = Product::first($query);
        $this->assertNotNull($product);
        $this->assertInstanceOf(Product::class, $product);
    }

    public function testFirstOrFailModel(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $query = [
            'query' => [
                'match' => [
                    'name' => $this->attributes['price']
                ]
            ]
        ];

        Product::firstOrFail($query);
    }

    public function testAllModels(): void
    {
        for ($i = 0; $i < 10; $i++) {
            Product::create($this->attributes);
        }

        $products = Product::all();

        $this->assertGreaterThanOrEqual(10, count($products));
    }
}
