<?php

namespace Laravolt\Workflow\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Laravolt\Camunda\Dto\Task;
use Laravolt\Workflow\Entities\Form;
use Laravolt\Workflow\Entities\Module;
use Laravolt\Workflow\Models\ProcessInstance;

class TaskCompleting
{
    use Dispatchable;

    /**
     * @var \Laravolt\Workflow\Entities\Module
     */
    public Module $module;

    /**
     * @var \Laravolt\Camunda\Dto\Task
     */
    public Task $task;

    /**
     * @var \Laravolt\Workflow\Entities\Form
     */
    public Form $form;

    /**
     * @var \Laravolt\Workflow\Models\ProcessInstance
     */
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
