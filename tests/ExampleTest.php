<?php

namespace Isswp101\Persimmon;

use Elasticsearch\ClientBuilder;
use Isswp101\Persimmon\Contracts\Storable;
use Isswp101\Persimmon\Model\IElasticsearchModel;
use Isswp101\Persimmon\Model\IEloquent;
use Isswp101\Persimmon\Models\Model;
use Isswp101\Persimmon\Repository\ElasticsearchCollectionParser;
use Isswp101\Persimmon\Repository\ElasticsearchRepository;

class ExampleTest extends \PHPUnit_Framework_TestCase
{
    /** @group failing */
    public function testTrueIsTrue()
    {

        $client = ClientBuilder::create()->build();
        $repository = new ElasticsearchRepository($client);
        $model = new Model();
        $model->id = 'my_id';
        $model->testField = 'abc';

//        echo 'PK=' . $model->getCollection();

        $model = $repository->all(null, Model::class, null, function(IEloquent $model) {
            $model->exists(true);
        });

//         $repository->insert($model);



        echo $model;

        $this->assertTrue(true);
    }
}