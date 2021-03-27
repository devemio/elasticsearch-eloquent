<?php

namespace Isswp101\Persimmon\Concerns;

use DateTime;

trait Timestampable
{
    protected bool $timestamps = true;

    private function touch(): void
    {
        if (!$this->timestamps) {
            return;
        }

        $dt = new DateTime();

        $now = $dt->format(DateTime::ISO8601);

        $this->created_at = $now;
        $this->updated_at = $now;
    }
}
