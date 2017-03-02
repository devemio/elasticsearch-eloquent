<?php

namespace Isswp101\Persimmon\Test;

use Isswp101\Persimmon\Test\Models\EventableModel;

class EventsTest extends BaseTestCase
{
    public static function setUpBeforeClass()
    {
        $hash = time();
        EventableModel::$_index = 'travis_ci_test_events' . $hash;
    }

    public function testSavingAndSaved()
    {
        $model = new EventableModel();
        $model->id = 1;
        $model->save();

        $path = $model->getPath()->toArray();
        $res = $this->es->get($path);

        $this->assertEquals(100500, $res['_source']['price']);
        $this->assertEquals(1050, $model->price);
    }

    public function testDeletingAndDelete()
    {
        $model = new EventableModel();
        $model->id = 2;
        $model->save();

        $model->delete();
        $this->assertTrue($model->_exist);

        $model->price = 100;

        $model->delete();
        $this->assertFalse($model->_exist);
        $this->assertEquals(1, $model->price);
    }

    public function testTearDown()
    {
        $this->deleteIndex(EventableModel::$_index);
    }
}
