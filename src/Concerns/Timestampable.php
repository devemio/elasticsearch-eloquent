<?php

namespace Isswp101\Persimmon\Concerns;

use DateTime;

trait Timestampable
{
    protected bool $timestamps = true;

    private function touch(bool $exists): void
    {
        if (!$this->timestamps) {
            return;
        }

        $dt = new DateTime();

        $now = $dt->format(DateTime::ISO8601);

        $this->created_at = !$exists ? $now : $this->created_at;
        $this->updated_at = $now;
    }
}
