<?php

declare(strict_types=1);

namespace Laravolt\Workflow\Http\Controllers;

use Laravolt\Camunda\Http\TaskClient;
use Laravolt\Workflow\Entities\Module;
use Laravolt\Workflow\TaskAccess;
use Laravolt\Workflow\WorkflowService;

class TaskController
{
    public function update(string $module, string $taskId)
    {
        $module = Module::make($module);
        $task = TaskClient::find(id: $taskId);

        abort_unless(
            TaskAccess::belongsToModule($module->processDefinitionKey, $module->tasks, $task->processDefinitionId, $task->taskDefinitionKey),
            404
        );
        abort_unless(TaskAccess::maySubmit($module->trackerTasks, $task->taskDefinitionKey, $task->assignee, auth()->id()), 403);

        app(WorkflowService::class)->submitTask($module, $task, request()->all());

        return redirect()->back()->with('success', __('Task completed'));
    }
}
