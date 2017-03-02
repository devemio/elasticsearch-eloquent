<?php

namespace Isswp101\Persimmon;

use Elasticsearch\ClientBuilder;
use Isswp101\Persimmon\Models\Model;
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
//        $repository->insert($model);

        echo $model;

        $this->assertTrue(true);
    }
}