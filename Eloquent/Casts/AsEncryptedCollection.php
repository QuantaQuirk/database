<?php

namespace QuantaQuirk\Database\Eloquent\Casts;

use QuantaQuirk\Contracts\Database\Eloquent\Castable;
use QuantaQuirk\Contracts\Database\Eloquent\CastsAttributes;
use QuantaQuirk\Support\Collection;
use QuantaQuirk\Support\Facades\Crypt;
use InvalidArgumentException;

class AsEncryptedCollection implements Castable
{
    /**
     * Get the caster class to use when casting from / to this cast target.
     *
     * @param  array  $arguments
     * @return \QuantaQuirk\Contracts\Database\Eloquent\CastsAttributes<\QuantaQuirk\Support\Collection<array-key, mixed>, iterable>
     */
    public static function castUsing(array $arguments)
    {
        return new class($arguments) implements CastsAttributes
        {
            public function __construct(protected array $arguments)
            {
            }

            public function get($model, $key, $value, $attributes)
            {
                $collectionClass = $this->arguments[0] ?? Collection::class;

                if (! is_a($collectionClass, Collection::class, true)) {
                    throw new InvalidArgumentException('The provided class must extend ['.Collection::class.'].');
                }

                if (isset($attributes[$key])) {
                    return new $collectionClass(Json::decode(Crypt::decryptString($attributes[$key])));
                }

                return null;
            }

            public function set($model, $key, $value, $attributes)
            {
                if (! is_null($value)) {
                    return [$key => Crypt::encryptString(Json::encode($value))];
                }

                return null;
            }
        };
    }
}
