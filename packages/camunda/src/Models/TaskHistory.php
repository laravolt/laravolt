<?php

declare(strict_types=1);

namespace Laravolt\Camunda\Models;

class TaskHistory extends Task
{
    protected function modelUri(): string
    {
        return 'history/task/';
    }

    public function fetch()
    {
        $attributes = $this->get('history/task?taskId=' . $this->id);

        foreach ($attributes as $key => $value) {
            $this->{$key} = $value;
        }

        return $this;
    }
}
