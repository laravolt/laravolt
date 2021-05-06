<?php

namespace Laravolt\Workflow\Http\Controllers;

use Illuminate\Contracts\View\View;
use Laravolt\Camunda\Http\ProcessInstanceClient;
use Laravolt\Workflow\Models\ProcessDefinition;
use Laravolt\Workflow\Models\ProcessInstance;

class InstancesController
{
    public function index(string $module): View
    {
        $config = config("laravolt.workflow-modules.$module");
        $definition = ProcessDefinition::where('key', $config['process_definition_key'])->firstOrFail();

        return view('laravolt::workflow.instances.index', compact('definition'));
    }
}
