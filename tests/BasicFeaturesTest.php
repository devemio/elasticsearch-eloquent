<?php

namespace Isswp101\Persimmon\Test;

use Carbon\Carbon;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Isswp101\Persimmon\Collection\ElasticsearchCollection;
use Isswp101\Persimmon\ElasticsearchModel;
use Isswp101\Persimmon\Exceptions\InvalidModelEndpointException;
use Isswp101\Persimmon\Model;
use Isswp101\Persimmon\QueryBuilder\Aggregations\TermsAggregation;
use Isswp101\Persimmon\QueryBuilder\Filters\IdsFilter;
use Isswp101\Persimmon\QueryBuilder\Filters\RangeOrExistFilter;
use Isswp101\Persimmon\QueryBuilder\Filters\TermFilter;
use Isswp101\Persimmon\QueryBuilder\QueryBuilder;
use Isswp101\Persimmon\Test\Models\InvalidModel;
use Isswp101\Persimmon\Test\Models\Product;

class BasicFeaturesTest extends BaseTestCase
{
    public static function setUpBeforeClass()
    {
        Product::$_index = 'travis_ci_test_' . time() . rand(1, 1000);
    }

    public function testValidateModel()
    {
        $this->expectException(InvalidModelEndpointException::class);
        new InvalidModel();
    }

    public function testPrepareIndex()
    {
        $index = Product::getIndex();
        $type = Product::getType();

        try {
            $this->es->indices()->delete(['index' => $index]);
        } catch (Missing404Exception $e) {
        }

        $this->sleep(3);

        $this->es->indices()->create(['index' => $index]);

        $this->sleep(3);

        $query = ['index' => $index, 'type' => $type, 'body' => ['query' => ['match_all' => new \stdClass()]]];
        $res = $this->es->search($query);
        $this->assertEquals(0, $res['hits']['total']);
    }

    public function testFill()
    {
        $p1 = new Product();
        $p1->id = 1;
        $p1->name = 'name';

        $p2 = new Product(['id' => 1, 'name' => 'name']);

        $this->assertSame($p1->toArray(), $p2->toArray());
    }

    public function testSave()
    {
        $product = new Product();
        $product->id = 1;
        $product->name = 'Product 1';
        $product->price = 20;

        $this->assertFalse($product->_exist);

        $product->save();

        $this->assertTrue($product->_exist);

        $this->assertInstanceOf(Model::class, $product);
        $this->assertInstanceOf(ElasticsearchModel::class, $product);

        $res = $this->es->get($product->getPath()->toArray());

        $this->assertEquals($product->getIndex(), $res['_index']);
        $this->assertEquals($product->getType(), $res['_type']);
        $this->assertEquals($product->getId(), $res['_id']);

        $this->assertEquals(1, $res['_id']);
        $this->assertEquals('Product 1', $res['_source']['name']);
        $this->assertEquals(20, $res['_source']['price']);

        $this->assertNotNull($res['_source']['created_at']);
        $this->assertNotNull($res['_source']['updated_at']);
        $this->assertInstanceOf(Carbon::class, $product->getCreatedAt());
        $this->assertInstanceOf(Carbon::class, $product->getUpdatedAt());
    }

    public function testFindBySpecifiedColumns()
    {
        $product = Product::find(1, ['name']);
        $this->assertEquals('Product 1', $product->name);
        $this->assertEquals(0, $product->price);
    }

    public function testFind()
    {
        $product = Product::find(1);

        $this->assertTrue($product->_exist);

        $this->assertEquals('Product 1', $product->name);
        $this->assertEquals('20', $product->price);
        $this->assertEquals(1, $product->getId());

        $this->assertInstanceOf(Model::class, $product);
        $this->assertInstanceOf(ElasticsearchModel::class, $product);
    }

    public function testUpdate()
    {
        $product = Product::find(1);
        $product->name = 'Product 2';
        $this->sleep(3);
        $product->save();

        $res = $this->es->get($product->getPath()->toArray());
        $this->assertEquals('Product 2', $res['_source']['name']);
        $this->assertNotSame($res['_source']['created_at'], $res['_source']['updated_at']);
    }

    public function testFindOrFail()
    {
        $this->expectException(ModelNotFoundException::class);
        Product::findOrFail(2);
    }

    public function testFindOrNew()
    {
        $product = Product::findOrNew(10);
        $this->assertFalse($product->_exist);
        $this->assertEquals(10, $product->getId());
        $this->assertEmpty($product->name);
        $this->assertEquals(0, $product->price);
        $this->assertInstanceOf(Model::class, $product);
        $this->assertInstanceOf(ElasticsearchModel::class, $product);

        $this->expectException(Missing404Exception::class);
        $this->es->get($product->getPath()->toArray());
    }

    public function testDelete()
    {
        Product::create(['id' => 6, 'name' => 'Product 6', 'price' => 66]);
        $product = Product::find(6);
        $this->assertNotNull($product);
        $this->assertTrue($product->_exist);
        $product->delete();
        $this->assertFalse($product->_exist);

        $this->expectException(Missing404Exception::class);
        $this->es->get($product->getPath()->toArray());

        $product = Product::find(6);
        $this->assertNull($product);
    }

    public function testDestroy()
    {
        Product::create(['id' => 5, 'name' => 'Product 5', 'price' => 55]);
        $product = Product::find(5);
        $this->assertNotNull($product);

        Product::destroy(5);

        $this->expectException(Missing404Exception::class);
        $this->es->get($product->getPath()->toArray());

        $product = Product::find(5);
        $this->assertNull($product);
    }

    public function testPartialUpdate()
    {
        $product = Product::find(1, ['name']);
        $product->name = 'Product 3';
        $product->save('name');

        $res = $this->es->get($product->getPath()->toArray());

        $this->assertEquals('Product 3', $res['_source']['name']);
        $this->assertEquals(20, $res['_source']['price']);
    }

    public function testBasicSearch()
    {
        $this->sleep(3);
        $products = Product::search();
        $product = $products->first();

        $this->assertInstanceOf(ElasticsearchCollection::class, $products);
        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals(1, $products->count());
        $this->assertEquals(1, $product->getId());
        $this->assertEquals(0, $product->_position);
        $this->assertEquals($products->count(), $products->getTotal());
        $this->assertNotNull($product->_score);
        $this->assertTrue($product->_exist);
    }

    public function testFirst()
    {
        $product = Product::first();
        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals(1, $product->getId());
        $this->assertEquals(0, $product->_position);
        $this->assertTrue($product->_exist);
    }

    public function testAll()
    {
        $products = Product::all();
        $this->assertInstanceOf(Collection::class, $products);
        $this->assertEquals(1, $products->count());
    }

    public function testMap()
    {
        $total = Product::map([], function (Product $product) {
            $this->assertInstanceOf(Product::class, $product);
            $this->assertEquals(1, $product->getId());
            $this->assertEquals(0, $product->_position);
            $this->assertTrue($product->_exist);
        });
        $this->assertEquals(1, $total);
    }

    public function testCreate()
    {
        $product = Product::create(['id' => 3, 'name' => 'Product 3', 'price' => 30]);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertTrue($product->_exist);

        $res = $this->es->get($product->getPath()->toArray());

        $this->assertEquals($product->getIndex(), $res['_index']);
        $this->assertEquals($product->getType(), $res['_type']);
        $this->assertEquals($product->getId(), $res['_id']);

        $this->assertEquals(3, $res['_id']);
        $this->assertEquals('Product 3', $res['_source']['name']);
        $this->assertEquals(30, $res['_source']['price']);

        $this->assertNotNull($res['_source']['created_at']);
        $this->assertNotNull($res['_source']['updated_at']);
        $this->assertInstanceOf(Carbon::class, $product->getCreatedAt());
        $this->assertInstanceOf(Carbon::class, $product->getUpdatedAt());
    }

    public function testBasicFilters()
    {
        Product::create(['id' => 1, 'name' => 'Product 1', 'price' => 10]);
        Product::create(['id' => 2, 'name' => 'Product 2', 'price' => 20]);
        Product::create(['id' => 3, 'name' => 'Product 3', 'price' => 30]);
        $this->sleep(3);

        $query = new QueryBuilder();
        $query->match('name', 'Product');
        $products = Product::search($query);
        $this->assertEquals(3, $products->count());

        $query = new QueryBuilder(['query' => ['match' => ['name' => 'Product']]]);
        $products = Product::search($query);
        $this->assertEquals(3, $products->count());

        $query = new QueryBuilder();
        $query->betweenOrEquals('price', 20, 30)->greaterThan('price', 15);
        $products = Product::search($query);
        $this->assertEquals(2, $products->count());

        $query = new QueryBuilder();
        $query->orMatch('name', '1')->orMatch('name', '2');
        $products = Product::search($query);
        $this->assertEquals(2, $products->count());

        $query = new QueryBuilder();
        $query->filter(new TermFilter('name', '2'));
        $products = Product::search($query);
        $this->assertEquals(1, $products->count());

        $query = new QueryBuilder();
        $query->filter(new TermFilter('name', ['2', '3']));
        $products = Product::search($query);
        $this->assertEquals(2, $products->count());
    }

    public function testAggregation()
    {
        $query = new QueryBuilder();
        $query->aggregation(new TermsAggregation('name'))->size(0);
        $products = Product::search($query);
        $buckets = $products->getAggregation('name');
        $this->assertEquals('product', $buckets[0]->getKey());
        $this->assertEquals(3, $buckets[0]->getCount());
    }

    public function testPagination()
    {
        $product = Product::find(1);
        $product->_position = 0;
        $product->makePagination();
        $this->assertEquals(3, $product->getPrevious()->getId());
        $this->assertEquals(2, $product->getNext()->getId());
    }

    public function testIdsFilter()
    {
        $query = new QueryBuilder();
        $query->filter(new IdsFilter([1, 3]));
        $products = Product::search($query);

        $this->assertEquals(2, $products->count());
        $this->assertEquals(1, $products->first()->getId());
        $this->assertEquals(3, $products->last()->getId());
    }

    public function testRangeOrExistFilter()
    {
        $query = new QueryBuilder();
        $query->filter(new RangeOrExistFilter('price', ['gte' => 20]));
        $products = Product::search($query);
        $this->assertEquals(2, $products->count());

        $query = new QueryBuilder();
        $query->filter(new RangeOrExistFilter('price'));
        $products = Product::search($query);
        $this->assertEquals(3, $products->count());
    }

    public function testTearDown()
    {
        $this->deleteIndex(Product::$_index);
    }
}
