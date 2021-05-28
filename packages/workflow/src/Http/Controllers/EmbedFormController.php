<?php

namespace Laravolt\Workflow\Http\Controllers;

use Laravolt\Workflow\Entities\Module;
use Laravolt\Workflow\WorkflowService;

class EmbedFormController
{
    public function show(string $key)
    {
        $module = Module::make('rekrutmen');

        return view('laravolt::workflow.embed-form.show', compact('module', 'key'));
    }

    public function store()
    {
        $module = Module::make('rekrutmen');
        /** @var \Laravolt\Workflow\Models\ProcessInstance $instance */
        $instance = app(WorkflowService::class)->start($module, request()->all());

        return redirect()->route('workflow::embed-tracking.show', $instance->getTrackingCode());
    }
}
