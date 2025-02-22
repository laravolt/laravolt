<?php

namespace Laravolt\Workflow\Http\Controllers;

use Laravolt\Camunda\Exceptions\ObjectNotFoundException;
use Laravolt\Camunda\Http\ProcessInstanceHistoryClient;
use Laravolt\Camunda\Http\TaskClient;
use Laravolt\Camunda\Http\TaskHistoryClient;
use Laravolt\Workflow\Entities\Module;
use Laravolt\Workflow\Models\ProcessInstance;

class EmbedTrackingController
{
    public function index(string $moduleId)
    {
        $module = Module::make($moduleId);
        $trackingCode = request('code');
        $instanceHistory = null;
        if ($trackingCode) {
            try {
                $instanceHistory = ProcessInstanceHistoryClient::find($trackingCode);
            } catch (ObjectNotFoundException $e) {
                // do nothing
            }
        }

        return view('laravolt::workflow.embed-tracking.index', compact('module', 'trackingCode', 'instanceHistory'));
    }

    public function show(string $moduleId, string $trackingCode)
    {
        $module = Module::make($moduleId);
        $schema = $module->trackerFormSchema();
        $instanceModel = ProcessInstance::find($trackingCode);

        try {
            $instanceHistory = ProcessInstanceHistoryClient::find($trackingCode);
        } catch (ObjectNotFoundException $e) {
            abort(404);
        }

        $completedTasks = TaskHistoryClient::getByProcessInstanceId($trackingCode);
        $ongoingTasks = TaskClient::getByProcessInstanceId($trackingCode);
        $availableTasks = collect($ongoingTasks)->filter(function ($item) use ($module) {
            return in_array($item->taskDefinitionKey, $module->trackerTasks);
        });

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
                'availableTasks',
                'completedTasks'
            )
        );
    }
}
