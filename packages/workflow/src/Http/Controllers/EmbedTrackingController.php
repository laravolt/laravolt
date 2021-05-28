<?php

namespace Laravolt\Workflow\Http\Controllers;

use Laravolt\Camunda\Http\TaskHistoryClient;
use Laravolt\Workflow\Entities\Module;
use Laravolt\Workflow\Models\ProcessInstance;

class EmbedTrackingController
{
    public function show(string $trackingCode)
    {
        $module = Module::make('rekrutmen');
        $instance = ProcessInstance::findOrFail($trackingCode);
        $definition = $instance->definition;
        $completedTasks = TaskHistoryClient::getByProcessInstanceId($trackingCode);
        $variables = $instance->variables->toArray();

        return view('laravolt::workflow.embed-tracking.show', compact('module', 'instance', 'variables', 'definition', 'completedTasks'));
    }
}
