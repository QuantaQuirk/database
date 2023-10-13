<?php

namespace QuantaQuirk\Database\Eloquent;

interface Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \QuantaQuirk\Database\Eloquent\Builder  $builder
     * @param  \QuantaQuirk\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model);
}
