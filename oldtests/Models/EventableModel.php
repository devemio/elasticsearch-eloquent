<?php

namespace Isswp101\Persimmon\Test\Models;

class EventableModel extends ElasticsearchModel
{
    public static $_index = 'test_events';
    public static $_type = 'events';

    public $name;
    public $price = 0;

    protected function saving()
    {
        $this->price = 100500;
    }

    protected function saved()
    {
        $this->price = 1050;
    }

    protected function deleting()
    {
        if ($this->price !== 100) {
            return false;
        }

        return true;
    }

    protected function deleted()
    {
        $this->price = 1;
    }
}
