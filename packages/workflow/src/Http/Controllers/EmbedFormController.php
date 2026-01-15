<?php

declare(strict_types=1);

namespace Laravolt\Workflow\Http\Controllers;

use Laravolt\Workflow\Entities\Module;
use Laravolt\Workflow\WorkflowService;

class EmbedFormController
{
    public function create(string $moduleId)
    {
        $module = Module::make($moduleId);

        return view('laravolt::workflow.embed-form.create', compact('module'));
    }

    public function store()
    {
        $module = Module::make('rekrutmen');
        /** @var \Laravolt\Workflow\Models\ProcessInstance $instance */
        $instance = app(WorkflowService::class)->start($module, request()->all());
        $successMessage = $module->message($module->startTaskKey(), 'success', 'Form successfully submitted');

        return redirect()
            ->route('workflow::tracker.show', [$module->id, $instance->getTrackingCode()])
            ->withSuccess($successMessage);
    }
}
