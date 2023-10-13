<?php

namespace QuantaQuirk\Database\Console\Seeds;

use QuantaQuirk\Database\Eloquent\Model;

trait WithoutModelEvents
{
    /**
     * Prevent model events from being dispatched by the given callback.
     *
     * @param  callable  $callback
     * @return callable
     */
    public function withoutModelEvents(callable $callback)
    {
        return fn () => Model::withoutEvents($callback);
    }
}
