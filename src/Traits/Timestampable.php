<?php

namespace Isswp101\Persimmon\Traits;

use Carbon\Carbon;

trait Timestampable
{
    protected function updateTimestamps()
    {
        $utc = Carbon::now('UTC')->toDateTimeString();
        $this->updated_at = $utc;
        if (!$this->exists) {
            $this->created_at = $utc;
        }
    }
}
