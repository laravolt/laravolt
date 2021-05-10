<?php

namespace Laravolt\Workflow\Http\Controllers;

use Illuminate\Contracts\View\View;
use Laravolt\Camunda\Http\ProcessInstanceClient;
use Laravolt\Workflow\Entities\Module;
use Laravolt\Workflow\Models\ProcessInstance;
use Laravolt\Workflow\WorkflowService;

class InstancesController
{
    public function index(string $module): View
    {
        $module = Module::make($module);

        return view('laravolt::workflow.instances.index', compact('module'));
    }

    public function show(string $module, string $id): View
    {
        $module = Module::make($module);
        $instance = ProcessInstance::findOrFail($id);
        $definition = $instance->definition;
        $completedTasks = ProcessInstanceClient::completedTasks($id);
        $openTasks = ProcessInstanceClient::tasks($id);
        $variables = $instance->variables->toArray();

        return view('laravolt::workflow.instances.show', compact('instance', 'definition', 'module', 'openTasks', 'completedTasks', 'variables'));
    }

    public function create(string $module): View
    {
        $module = Module::make($module);

        return view('laravolt::workflow.instances.create', compact('module'));
    }

    public function store(string $module)
    {
        $module = Module::make($module);
        app(WorkflowService::class)->start($module, request()->all());

        return redirect()->route('workflow::module.instances.index', $module->id);
    }
}
