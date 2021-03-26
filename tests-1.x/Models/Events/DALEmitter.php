<?php

namespace Isswp101\Persimmon\Test\Models\Events;

use Isswp101\Persimmon\DAL\DALEvents;
use Isswp101\Persimmon\Event\EventEmitter;

class DALEmitter extends EventEmitter
{
    public function __construct()
    {
        $this->on(DALEvents::BEFORE_SEARCH, function (array $params) {
            // Logging elasticsearch queries
            // Log::debug('Query', $params);
        });
    }
}
