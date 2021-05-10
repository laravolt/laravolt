<?php

namespace Laravolt\Workflow;

use Laravolt\Camunda\Dto\Task;
use Laravolt\Camunda\Http\ProcessDefinitionClient;
use Laravolt\Camunda\Http\TaskClient;
use Laravolt\Workflow\Entities\Form;
use Laravolt\Workflow\Entities\Module;
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
        $form = new Form(schema: $module->startFormSchema(), data: $data);
        $instance = ProcessDefinitionClient::start(
            key: $module->processDefinitionKey,
            variables: $form->toCamundaVariables()
        );

        return ProcessInstance::sync($instance);
    }

    public function submitTask(Module $module, Task $task, array $data)
    {
        $formSchema = $module->formSchema($task->taskDefinitionKey);
        $form = new Form(schema: $formSchema, data: $data);
        $variables = TaskClient::submit($task->id, $form->toCamundaVariables());
        $instance = ProcessInstance::find($task->processInstanceId);
        $instance->variables = $instance->variables->merge($variables);
    }
}
