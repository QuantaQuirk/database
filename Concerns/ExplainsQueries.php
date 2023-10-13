<?php

namespace QuantaQuirk\Database\Concerns;

use QuantaQuirk\Support\Collection;

trait ExplainsQueries
{
    /**
     * Explains the query.
     *
     * @return \QuantaQuirk\Support\Collection
     */
    public function explain()
    {
        $sql = $this->toSql();

        $bindings = $this->getBindings();

        $explanation = $this->getConnection()->select('EXPLAIN '.$sql, $bindings);

        return new Collection($explanation);
    }
}
