<?php

namespace Laravolt\Workflow\Http\Controllers;

use Laravolt\Camunda\Http\ProcessInstanceHistoryClient;
use Laravolt\Camunda\Http\TaskHistoryClient;
use Laravolt\Workflow\Entities\Module;
use Laravolt\Workflow\Models\ProcessInstance;

class EmbedTrackingController
{
    public function show(string $trackingCode)
    {
        $instanceModel = ProcessInstance::findOrFail($trackingCode);
        $instanceHistory = ProcessInstanceHistoryClient::find($trackingCode);
        $completedTasks = TaskHistoryClient::getByProcessInstanceId($trackingCode);

        return view(
            'laravolt::workflow.embed-tracking.show',
            compact('instanceModel', 'instanceHistory', 'completedTasks')
        );
    }
}
