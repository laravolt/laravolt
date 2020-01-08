<?php

declare(strict_types=1);

namespace Laravolt\Workflow\Entities;

use Illuminate\Database\Eloquent\Model;
use Laravolt\Camunda\Models\Task;
use Laravolt\Workflow\Traits\DataRetrieval;

class TaskAttributes
{
    use DataRetrieval;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var Task
     */
    protected $task;

    /**
     * @var array
     */
    protected $taskConfig;

    /**
     * @var array
     */
    protected $data;

    public function __construct(Model $user, Task $task, array $taskConfig)
    {
        $this->user = $user;
        $this->task = $task;
        $this->taskConfig = $taskConfig;
        $this->data = $this->getDataByProcessInstanceId($task->processInstanceId);
    }
}
