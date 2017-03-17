<?php

namespace Isswp101\Persimmon;

use Elasticsearch\ClientBuilder;
use Isswp101\Persimmon\Cache\ICache;
use Isswp101\Persimmon\Collection\ElasticsearchCollection;
use Isswp101\Persimmon\Contracts\Storable;
use Isswp101\Persimmon\DI\DI;
use Isswp101\Persimmon\Helpers\Bulk;
use Isswp101\Persimmon\Model\ElasticsearchModel;
use Isswp101\Persimmon\Model\IElasticsearchModel;
use Isswp101\Persimmon\Model\IEloquent;
use Isswp101\Persimmon\Models\Branch;
use Isswp101\Persimmon\Models\Employee;
use Isswp101\Persimmon\Models\Model;
use Isswp101\Persimmon\Models\TestFilter;
use Isswp101\Persimmon\QueryBuilder\QueryBuilder;
use Isswp101\Persimmon\CollectionParser\ElasticsearchCollectionParser;
use Isswp101\Persimmon\Repository\ElasticsearchRepository;
use Isswp101\Persimmon\Repository\ICacheRepository;
use Isswp101\Persimmon\Repository\IRepository;
use Isswp101\Persimmon\Repository\RuntimeCacheRepository;
use ONGR\ElasticsearchDSL\Aggregation\Bucketing\TermsAggregation;
use ONGR\ElasticsearchDSL\InnerHit\ParentInnerHit;
use ONGR\ElasticsearchDSL\Query\FullText\MatchQuery;
use ONGR\ElasticsearchDSL\Search;
use SebastianBergmann\CodeCoverage\Report\PHP;

class Debug
{
    public static $i = 0;
    public static $j = 0;
}

class ExampleTest extends \PHPUnit_Framework_TestCase
{
    /** @group failing */
    public function testTrueIsTrue()
    {
        $model = Model::find('my_id', ['testField']);
        $model = Model::find('my_id', ['id']);
        $model = Model::find('my_id');
        dd($model);


        $model = Model::find('my_id', ['id']);
        $model = Model::find('my_id', ['id']);
        $model = Model::find('my_id', ['id']);
        $model = Model::find('my_id', ['id']);
        $model = Model::find('my_id', ['testField']);
        $model = Model::find('my_id', ['testField']);
        $model = Model::find('my_id', ['testField']);
        $model = Model::find('my_id', ['id']);
        $model = Model::find('my_id', ['id']);
        dd($model);


        var_dump(DI::make('elasticsearch')->getCacheRepository());
        $model = Model::find('my_id', ['id']);
        dd($model);
//        $model = new Model();
//        $model->id = 1;


//        $search = [
//            'query' => [
//                'has_child' => [
//                    'type' => 'employee',
//                    'query' => [
//                        'match' => [
//                            'name' => 'Vasya1'
//                        ]
//                    ],
//                    'inner_hits' => new \stdClass()
//                ]
//            ]
//        ];
//
//        $branches = Branch::all(new QueryBuilder($search));


//        $branch = Branch::find('london');
//        $employees = $branch->employees()->get();

        /** @var Branch $branch */
        $branch = $branches->first();


        dd($branches);
//
//
//
//        $employee = new Employee();
//        $employee->setPrimaryKey(5);
//        $employee->name = 'Vasya1';
//        $employee->dob = '1970-10-25';
//        $employee->hobby = 'no';
//        $employee->branch()->associate($branch);
//        // $employee->save();
//
//        Bulk::index([$employee]);

//        $employee = Employee::find('1:london');
//        dd($employee);

        $branch = $employee->branch()->get();
//        dd($employee);
//        $employee->save();
//        $branch = $employee->branch()->get();
//
        dd($branch);
//        dd($employee);

        $models = Model::all(new QueryBuilder());
        dd($models);



//        dd(Model::find('my_id', ['testField']));

//        $client = ClientBuilder::create()->build();
//        $repository = new ElasticsearchRepository($client);
        $model = new Model();
        $model->id = 'my_id_1';
        $model->testField = 'abc_1';
        $model->save();
        dd($model);

//        $matchAll = new \ONGR\ElasticsearchDSL\Query\MatchAllQuery();

        $termsAggregation = new TermsAggregation('testFields', 'testField');
        $search = new Search();
        $search->addAggregation($termsAggregation);

        $models = Model::all((new QueryBuilder($search)));

        dd($models);

//        $model->save();
//        $repository->insert($model);

        /** @var Model $model */
//        $model = $repository->find('my_id', Model::class);
//        $model->testField = 'abc1';
//        $repository->insert($model);

//        $model = $repository->find('my_id', Model::class);

//        echo 'PK=' . $model->getCollection();

//        $models = $repository->all(new QueryBuilder(), Model::class, [], function(Model $model) {
//            $model->exists(true);
//        });

        echo PHP_EOL . PHP_EOL;
        echo PHP_EOL . PHP_EOL . $models;

        $this->assertTrue(true);
    }
}