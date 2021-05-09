<?php

namespace Laravolt\Workflow;

use Laravolt\Camunda\Http\ProcessDefinitionClient;
use Laravolt\Camunda\Http\ProcessInstanceClient;
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
}
