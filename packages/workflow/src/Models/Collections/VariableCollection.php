<?php

namespace Laravolt\Workflow\Models\Collections;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Collection;
use Laravolt\Camunda\Dto\Variable;

class VariableCollection extends Collection implements Castable
{
    public function getValue($key, $default = null)
    {
        return $this->offsetExists($key) ? $this->get($key)->value : $default;
    }

    public function toArray()
    {
        return $this->map(fn (Variable $var) => $var->value)->all();
    }

    public static function castUsing(array $arguments)
    {
        return new class() implements CastsAttributes {
            public function get($model, $key, $value, $attributes)
            {
                if (isset($attributes[$key])) {
                    $variables = json_decode($attributes[$key], true);
                    $collection = new VariableCollection();
                    foreach ($variables as $name => $variable) {
                        $collection->offsetSet(
                            $variable['name'] ?? $name,
                            new Variable(
                                name: $variable['name'] ?? null,
                                type: $variable['type'] ?? null,
                                value: $variable['value'] ?? null
                            )
                        );
                    }

                    return $collection;
                }

                return null;
            }

            public function set($model, $key, $value, $attributes)
            {
                return [$key => json_encode($value ?? [])];
            }
        };
    }
}
