<?php

namespace Laravolt\Workflow\Http\Controllers;

use Laravolt\Camunda\Http\ProcessInstanceHistoryClient;
use Laravolt\Camunda\Http\TaskClient;
use Laravolt\Camunda\Http\TaskHistoryClient;
use Laravolt\Workflow\Entities\Module;
use Laravolt\Workflow\Models\ProcessInstance;

class EmbedTrackingController
{
    public function show(string $moduleId, string $trackingCode)
    {
        $module = Module::make($moduleId);
        $schema = $module->trackerFormSchema();
        $instanceModel = ProcessInstance::find($trackingCode.'x');
        $instanceHistory = ProcessInstanceHistoryClient::find($trackingCode);
        $completedTasks = TaskHistoryClient::getByProcessInstanceId($trackingCode);
        $ongoingTasks = TaskClient::getByProcessInstanceId($trackingCode);

        if ($instanceModel instanceof ProcessInstance) {
            $variables = $instanceModel->variables->toArray();
        } else {
            $variables = collect(ProcessInstanceHistoryClient::variables($trackingCode))
                ->transform(fn ($item) => $item->value)
                ->toArray();
        }

        return view(
            'laravolt::workflow.embed-tracking.show',
            compact(
                'trackingCode',
                'module',
                'schema',
                'variables',
                'instanceModel',
                'instanceHistory',
                'ongoingTasks',
                'completedTasks'
            )
        );
    }
}
