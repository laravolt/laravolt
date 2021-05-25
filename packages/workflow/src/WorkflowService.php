<?php

namespace Laravolt\Workflow;

use Laravolt\Camunda\Dto\Task;
use Laravolt\Camunda\Http\ProcessDefinitionClient;
use Laravolt\Camunda\Http\TaskClient;
use Laravolt\Workflow\Entities\Form;
use Laravolt\Workflow\Entities\Module;
use Laravolt\Workflow\Events\ProcessInstanceStarting;
use Laravolt\Workflow\Events\TaskCompleted;
use Laravolt\Workflow\Events\TaskCompleting;
use Laravolt\Workflow\Models\ProcessInstance;

class WorkflowService
{
    /**
     * WorkflowService constructor.
     */
    public function __construct()
    {
    }

    public function start(Module $module, array $data): ProcessInstance
    {
        // Registering events
        $module->registerTaskEvents($module->startTaskKey());

        $form = new Form(schema: $module->startFormSchema(), data: $data);

        ProcessInstanceStarting::dispatch($module, $form);

        $instance = ProcessDefinitionClient::start(
            key: $module->processDefinitionKey,
            variables: $form->toCamundaVariables()
        );

        return ProcessInstance::sync($instance);
    }

    public function submitTask(Module $module, Task $task, array $data)
    {
        // Registering events
        $module->registerTaskEvents($task->taskDefinitionKey);

        $instance = ProcessInstance::findOrFail($task->processInstanceId);

        // Submit to Camunda API
        $formSchema = $module->formSchema($task->taskDefinitionKey);
        $form = new Form(schema: $formSchema, data: $data);

        TaskCompleting::dispatch($module, $instance, $task, $form);

        $variables = TaskClient::submit($task->id, $form->toCamundaVariables());

        // Update local data
        $tasks = collect(TaskClient::getByProcessInstanceId($instance->id))->pluck('taskDefinitionKey');
        $instance->variables = $instance->variables->merge($variables);
        $instance->tasks = $tasks;
        $instance->save();

        TaskCompleted::dispatch($module, $instance, $task, $form);
    }
}
