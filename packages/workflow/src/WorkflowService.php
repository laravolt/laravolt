<?php

namespace Laravolt\Workflow;

use Laravolt\Camunda\Http\ProcessDefinitionClient;
use Laravolt\Workflow\Entities\Form;
use Laravolt\Workflow\Entities\Module;

class WorkflowService
{
    /**
     * WorkflowService constructor.
     */
    public function __construct()
    {
    }

    public function start(Module $module, Form $form)
    {
        $data = $form->toCamundaVariables();
        $instance = ProcessDefinitionClient::start(key: $module->processDefinitionKey, variables: $data);
        dd($instance);
        //TODO 1: format $data agar compatible untuk disimpan di DB dan dikirim ke Camunda REST
        //TODO 2: start instance via REST
        //TODO 3: simpan variables ke DB wf_process_instances
        //TODO 4: trigger events PROCESS_STARTED
    }
}
