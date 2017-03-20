<?php
declare(strict_types = 1);

namespace Isswp101\Persimmon;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Elasticsearch\Common\Exceptions\BadRequest400Exception;
use Isswp101\Persimmon\Collection\ICollection;
use Isswp101\Persimmon\Collection\IElasticsearchCollection;
use Isswp101\Persimmon\CollectionParser\ElasticsearchCollectionParser;
use Isswp101\Persimmon\Contracts\Storable;
use Isswp101\Persimmon\DI\Container;
use Isswp101\Persimmon\DI\DI;
use Isswp101\Persimmon\Helpers\Bulk;
use Isswp101\Persimmon\Model\ElasticsearchModel;
use Isswp101\Persimmon\Model\Eloquent;
use Isswp101\Persimmon\Model\IElasticsearchModel;
use Isswp101\Persimmon\Model\IEloquent;
use Isswp101\Persimmon\Models\Model;
use Isswp101\Persimmon\QueryBuilder\QueryBuilder;
use Isswp101\Persimmon\Repository\ElasticsearchRepository;
use Isswp101\Persimmon\Repository\IRepository;
use Isswp101\Persimmon\Repository\RuntimeCacheRepository;

class ExampleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var IRepository
     */
    protected $repository;

    /**
     * @var IRepository
     */
    protected $cacheRepository;

    protected function setUp()
    {
        $this->client = ClientBuilder::create()->build();
        $this->repository = new ElasticsearchRepository($this->client);
        $this->cacheRepository = new RuntimeCacheRepository();
        DI::bind('elasticsearch', function () {
            return new Container($this->repository, $this->cacheRepository);
        }, DI::SINGLETON);

        $collection = new ElasticsearchCollectionParser(Model::getCollection());
        if ($this->client) {
            try {
                $this->client->indices()->create(['index' => $collection->getIndex()]);
            } catch (BadRequest400Exception $ingnored) {
            }
        }
    }

    protected function tearDown()
    {
        $collection = new ElasticsearchCollectionParser(Model::getCollection());
        if ($this->client) {
            $this->client->indices()->delete(['index' => $collection->getIndex()]);
        }
        unset($this->client, $this->repository, $this->cacheRepository);
    }

    public function testSingleDocumentIndexing()
    {
        $model = new Model();
        $model->setPrimaryKey('my_id');
        $model->testField = 'abc';
        $model->save();
        $actualModel = $this->repository->find('my_id', Model::class);
        $modelByCreateMethod = Model::create(['id' => 'my_id1', 'testField' => 'abc']);
        $actualModelByCreateMethod = $this->repository->find('my_id1', Model::class);
        $this->assertInstanceOf(Model::class, $model);
        $this->assertInstanceOf(IElasticsearchModel::class, $model);
        $this->assertInstanceOf(ElasticsearchModel::class, $model);
        $this->assertInstanceOf(IEloquent::class, $model);
        $this->assertInstanceOf(Eloquent::class, $model);
        $this->assertInstanceOf(Storable::class, $model);
        $this->assertNotNull($actualModel);
        $this->assertNotNull($actualModelByCreateMethod);
        $this->assertNotNull($model->getPrimaryKey());
        $this->assertNotNull($modelByCreateMethod->getPrimaryKey());
        $this->assertTrue($model->exists());
        $this->assertTrue($modelByCreateMethod->exists());
        $this->assertEquals($model->toArray(), $actualModel->toArray());
        $this->assertEquals($modelByCreateMethod->toArray(), $actualModelByCreateMethod->toArray());
    }

    public function testBulkIndexing()
    {
        $m1 = new Model(['id' => 1, 'testField' => 'abc']);
        $m2 = new Model(['id' => 2, 'testField' => 'bcd']);
        $m3 = new Model(['id' => 3, 'testField' => 'cde']);
        Bulk::index($this->client, [$m1, $m2, $m3]);
        $am1 = $this->repository->find('1', Model::class);
        $am2 = $this->repository->find('2', Model::class);
        $am3 = $this->repository->find('3', Model::class);
        $this->assertEquals($m1->toArray(), $am1->toArray());
        $this->assertEquals($m2->toArray(), $am2->toArray());
        $this->assertEquals($m3->toArray(), $am3->toArray());
    }

    public function testGettingDocuments()
    {
        $data = ['id' => 'my_id', 'testField' => 'abc'];
        Model::create($data);
        $model = Model::find('my_id');
        $this->assertNotNull($model);
        $this->assertTrue($model->exists());
        $this->assertEquals($model->toArray(), $data);
        $this->assertInstanceOf(Model::class, $model);
        $this->assertInstanceOf(IElasticsearchModel::class, $model);
        $this->assertInstanceOf(ElasticsearchModel::class, $model);
        $this->assertInstanceOf(IEloquent::class, $model);
        $this->assertInstanceOf(Eloquent::class, $model);
        $this->assertInstanceOf(Storable::class, $model);
    }

    public function testUpdatingDocuments()
    {
        $model = Model::create(['id' => 'my_id', 'testField' => 'abc']);
        $model->testField = 'bcd';
        $model->save();
        $actualModel = $this->repository->find('my_id', Model::class);
        $this->assertEquals($model->testField, $actualModel->testField);
    }

    public function testDeletingDocuments()
    {
        $model = Model::create(['id' => 'my_id', 'testField' => 'abc']);
        $model->delete();
        $actualModel = $this->repository->find('my_id', Model::class);
        $this->assertNull($actualModel);
    }

    public function testSearchOperations()
    {
        Model::create(['id' => 1, 'testField' => 'abc']);
        Model::create(['id' => 2, 'testField' => 'bcd']);
        Model::create(['id' => 3, 'testField' => 'cde']);
        sleep(5); // @TODO: fix it
        $models = Model::all(new QueryBuilder());
        $query = [
            'query' => [
                'match' => [
                    'testField' => 'abc'
                ]
            ]
        ];
        $abcModels = Model::all(new QueryBuilder($query));
        $this->assertInstanceOf(ICollection::class, $models);
        $this->assertInstanceOf(IElasticsearchCollection::class, $models);
        $this->assertEquals(3, $models->count());
        $this->assertEquals(1, $abcModels->count());
    }
}