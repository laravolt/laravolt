<?php

namespace Laravolt\Workflow\Http\Controllers;

use Illuminate\Contracts\View\View;
use Laravolt\Workflow\Models\ProcessDefinition;
use Laravolt\Workflow\Models\ProcessInstance;

class InstancesController
{
    public function show(ProcessInstance $instance): View
    {
        $definition = ProcessDefinition::find($instance->definition_id);

        return view('laravolt::workflow.instances.show', compact('instance', 'definition'));
    }
}
