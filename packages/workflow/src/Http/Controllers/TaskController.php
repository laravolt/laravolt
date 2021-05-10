<?php

namespace Laravolt\Workflow\Http\Controllers;

use Laravolt\Camunda\Http\TaskClient;
use Laravolt\Workflow\Entities\Module;
use Laravolt\Workflow\WorkflowService;

class TaskController
{
    public function update(string $module, string $taskId)
    {
        $module = Module::make($module);
        $task = TaskClient::find(id: $taskId);

        app(WorkflowService::class)->submitTask($module, $task, request()->all());

        return redirect()->back()->with('success', __('Task completed'));
    }
}
