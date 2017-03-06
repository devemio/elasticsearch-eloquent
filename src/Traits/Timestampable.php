<?php

namespace Isswp101\Persimmon\Traits;

use Carbon\Carbon;

trait Timestampable
{
    protected function updateTimestamps()
    {
        $utc = Carbon::now('UTC')->toDateTimeString();
        $this->{static::UPDATED_AT} = $utc;
        if (!$this->exists) {
            $this->{static::CREATED_AT} = $utc;
        }
    }
}
