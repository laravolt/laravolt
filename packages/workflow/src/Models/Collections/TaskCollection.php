<?php

namespace Laravolt\Workflow\Models\Collections;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Collection;
use Laravolt\Camunda\Dto\Task;

class TaskCollection extends Collection implements Castable
{
    public static function castUsing(array $arguments)
    {
        return new class implements CastsAttributes {
            public function get($model, $key, $value, $attributes)
            {
                if (isset($attributes[$key])) {
                    $tasks = json_decode($attributes[$key], true);
                    $collection = new TaskCollection();
                    foreach ($tasks as $task) {
                        $collection->add(new Task($task));
                    }

                    return $collection;
                }

                return null;
            }

            public function set($model, $key, $value, $attributes)
            {
                return [$key => json_encode($value->toArray())];
            }
        };
    }
}
