<?php

namespace Laravolt\Workflow\Http\Controllers;

use Illuminate\Contracts\View\View;
use Laravolt\Camunda\Http\ProcessInstanceClient;
use Laravolt\Workflow\Models\ProcessDefinition;
use Laravolt\Workflow\Models\ProcessInstance;

class InstancesController
{
    public function show(ProcessInstance $instance): View
    {
        request()->all()
        $definition = ProcessDefinition::where('key', $instance->definition_key)->firstOrFail();
        $completedTasks = ProcessInstanceClient::completedTasks($instance->id);
        $tasks = ProcessInstanceClient::tasks($instance->id);
        dd($tasks, $completedTasks);

        return view('laravolt::workflow.instances.show', compact('instance', 'definition'));
    }
}
