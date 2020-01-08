<?php

namespace Laravolt\Workflow\Policies;

use App\User;
use Illuminate\Support\Arr;
use Laravolt\Camunda\Models\Task;

class TaskPolicy
{
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function edit(User $user, Task $task, array $taskConfig)
    {
        $readonly = Arr::get($taskConfig, 'attributes.readonly', false);
        if (is_string($readonly) && class_exists($readonly)) {
            $readonly = (new $readonly($user, $task, $taskConfig))->readonly();
        }
        if ($readonly) {
            return false;
        }

        return true;
    }
}
