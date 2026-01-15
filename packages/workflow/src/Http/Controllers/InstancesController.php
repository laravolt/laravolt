<?php

declare(strict_types=1);

namespace Laravolt\Workflow\Http\Controllers;

use Illuminate\Contracts\View\View;
use Laravolt\Camunda\Http\ProcessInstanceClient;
use Laravolt\Camunda\Http\ProcessInstanceHistoryClient;
use Laravolt\Camunda\Http\TaskClient;
use Laravolt\Camunda\Http\TaskHistoryClient;
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

    public function show(string $moduleId, string $id): View
    {
        $module = Module::make($moduleId);
        $instance = ProcessInstance::find($id) ?? ProcessInstanceHistoryClient::find($id);
        $completedTasks = TaskHistoryClient::getByProcessInstanceId($id);
        $openTasks = TaskClient::getByProcessInstanceId($id);

        if ($instance instanceof ProcessInstance) {
            $variables = $instance->variables->toArray();
        } else {
            $variables = collect(ProcessInstanceClient::variables($id))->transform(fn ($item) => $item->value)->toArray();
        }

        return view('laravolt::workflow.instances.show', compact('instance', 'module', 'openTasks', 'completedTasks', 'variables'));
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
