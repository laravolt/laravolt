<?php

declare(strict_types=1);

namespace Laravolt\Workflow\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Laravolt\Camunda\Dto\Task;
use Laravolt\Workflow\Entities\Form;
use Laravolt\Workflow\Entities\Module;
use Laravolt\Workflow\Models\ProcessInstance;

class TaskCompleted
{
    use Dispatchable;

    public Module $module;

    public Task $task;

    public Form $form;

    public ProcessInstance $instance;

    /**
     * TaskSubmitting constructor.
     */
    public function __construct(Module $module, ProcessInstance $instance, Task $task, Form $form)
    {
        $this->module = $module;
        $this->instance = $instance;
        $this->task = $task;
        $this->form = $form;
    }
}
