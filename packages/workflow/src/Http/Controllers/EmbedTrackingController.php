<?php

declare(strict_types=1);

namespace Laravolt\Workflow\Http\Controllers;

use Laravolt\Camunda\Dto\ProcessInstanceHistory;
use Laravolt\Camunda\Exceptions\ObjectNotFoundException;
use Laravolt\Camunda\Http\ProcessDefinitionClient;
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
        $instanceHistory = $trackingCode ? $this->findInstance($module, (string) $trackingCode) : null;

        return view('laravolt::workflow.embed-tracking.index', compact('module', 'trackingCode', 'instanceHistory'));
    }

    public function show(string $moduleId, string $trackingCode)
    {
        $module = Module::make($moduleId);
        $instanceHistory = $this->findInstance($module, $trackingCode);
        abort_if($instanceHistory === null, 404);

        $schema = $module->trackerFormSchema();
        $instanceModel = ProcessInstance::find($trackingCode);

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

    public function diagram(string $moduleId, string $trackingCode)
    {
        $instance = $this->findInstance(Module::make($moduleId), $trackingCode);
        abort_if($instance === null, 404);

        try {
            $xml = ProcessDefinitionClient::xml($instance->processDefinitionId);
        } catch (ObjectNotFoundException) {
            abort(404);
        }

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }

    /**
     * A tracking code opens only an instance of this module's process, never one of
     * another module (or of a process with no module at all).
     */
    private function findInstance(Module $module, string $trackingCode): ?ProcessInstanceHistory
    {
        try {
            $instance = ProcessInstanceHistoryClient::find($trackingCode);
        } catch (ObjectNotFoundException) {
            return null;
        }

        return $instance->processDefinitionKey === $module->processDefinitionKey ? $instance : null;
    }
}
