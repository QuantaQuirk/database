<?php

namespace QuantaQuirk\Database\Eloquent;

use BadMethodCallException;
use QuantaQuirk\Database\Eloquent\Relations\HasMany;
use QuantaQuirk\Support\Str;

class PendingHasThroughRelationship
{
    /**
     * The root model that the relationship exists on.
     *
     * @var \QuantaQuirk\Database\Eloquent\Model
     */
    protected $rootModel;

    /**
     * The local relationship.
     *
     * @var \QuantaQuirk\Database\Eloquent\Relations\HasMany|\QuantaQuirk\Database\Eloquent\Relations\HasOne
     */
    protected $localRelationship;

    /**
     * Create a pending has-many-through or has-one-through relationship.
     *
     * @param  \QuantaQuirk\Database\Eloquent\Model  $rootModel
     * @param  \QuantaQuirk\Database\Eloquent\Relations\HasMany|\QuantaQuirk\Database\Eloquent\Relations\HasOne  $localRelationship
     */
    public function __construct($rootModel, $localRelationship)
    {
        $this->rootModel = $rootModel;

        $this->localRelationship = $localRelationship;
    }

    /**
     * Define the distant relationship that this model has.
     *
     * @param  string|(callable(\QuantaQuirk\Database\Eloquent\Model): (\QuantaQuirk\Database\Eloquent\Relations\HasOne|\QuantaQuirk\Database\Eloquent\Relations\HasMany))  $callback
     * @return \QuantaQuirk\Database\Eloquent\Relations\HasManyThrough|\QuantaQuirk\Database\Eloquent\Relations\HasOneThrough
     */
    public function has($callback)
    {
        if (is_string($callback)) {
            $callback = fn () => $this->localRelationship->getRelated()->{$callback}();
        }

        $distantRelation = $callback($this->localRelationship->getRelated());

        if ($distantRelation instanceof HasMany) {
            return $this->rootModel->hasManyThrough(
                $distantRelation->getRelated()::class,
                $this->localRelationship->getRelated()::class,
                $this->localRelationship->getForeignKeyName(),
                $distantRelation->getForeignKeyName(),
                $this->localRelationship->getLocalKeyName(),
                $distantRelation->getLocalKeyName(),
            );
        }

        return $this->rootModel->hasOneThrough(
            $distantRelation->getRelated()::class,
            $this->localRelationship->getRelated()::class,
            $this->localRelationship->getForeignKeyName(),
            $distantRelation->getForeignKeyName(),
            $this->localRelationship->getLocalKeyName(),
            $distantRelation->getLocalKeyName(),
        );
    }

    /**
     * Handle dynamic method calls into the model.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (Str::startsWith($method, 'has')) {
            return $this->has(Str::of($method)->after('has')->lcfirst()->toString());
        }

        throw new BadMethodCallException(sprintf(
            'Call to undefined method %s::%s()', static::class, $method
        ));
    }
}
