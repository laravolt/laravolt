<?php

declare(strict_types=1);

namespace Laravolt\Camunda\Models;

use Illuminate\Support\Arr;

class TaskHistory extends CamundaModel
{
    protected $processInstance;

    protected function modelUri(): string
    {
        return 'history/task/';
    }

    public function processInstance(): ProcessInstanceHistory
    {
        if (!$this->processInstance && $this->processInstanceId) {
            $this->processInstance = (new ProcessInstanceHistory($this->processInstanceId))->fetch();
        }

        return $this->processInstance;
    }

    public function fetch()
    {
        $attributes = Arr::first($this->get('history/task?taskId='.$this->id));

        foreach ($attributes as $key => $value) {
            $this->{$key} = $value;
        }

        return $this;
    }
}
