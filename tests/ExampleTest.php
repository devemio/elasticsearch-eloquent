<?php

namespace Isswp101\Persimmon;

use Elasticsearch\ClientBuilder;
use Isswp101\Persimmon\Collection\ElasticsearchCollection;
use Isswp101\Persimmon\Contracts\Storable;
use Isswp101\Persimmon\Model\IElasticsearchModel;
use Isswp101\Persimmon\Model\IEloquent;
use Isswp101\Persimmon\Models\Model;
use Isswp101\Persimmon\QueryBuilder\QueryBuilder;
use Isswp101\Persimmon\CollectionParser\ElasticsearchCollectionParser;
use Isswp101\Persimmon\Repository\ElasticsearchRepository;
use SebastianBergmann\CodeCoverage\Report\PHP;

class ExampleTest extends \PHPUnit_Framework_TestCase
{
    /** @group failing */
    public function testTrueIsTrue()
    {
//        $client = ClientBuilder::create()->build();
//        $repository = new ElasticsearchRepository($client);
//        $model = new Model();
//        $model->id = 'my_id_1';
//        $model->testField = 'abc_1';
//        $model->save();

        $matchAll = new \ONGR\ElasticsearchDSL\Query\MatchAllQuery();
        $search = new \ONGR\ElasticsearchDSL\Search();
        $search->addQuery($matchAll);


        $models = Model::all(new QueryBuilder($search));

        dd($models->count());

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