<?php

namespace Laravolt\Workflow\Http\Controllers;

use Illuminate\Contracts\View\View;
use Laravolt\Workflow\Entities\Form;
use Laravolt\Workflow\Models\ProcessInstance;
use Laravolt\Workflow\Entities\Module;
use Laravolt\Workflow\WorkflowService;

class InstancesController
{
    public function index(string $module): View
    {
        $module = Module::make($module);

        return view('laravolt::workflow.instances.index', compact('module'));
    }

    public function show(string $module, string $id): View
    {
        $instance = ProcessInstance::findOrFail($id);
        $definition = $instance->definition;

        return view('laravolt::workflow.instances.show', compact('instance', 'definition'));
    }

    public function create(string $module): View
    {
        $module = Module::make($module);

        return view('laravolt::workflow.instances.create', compact('module'));
    }

    public function store(string $module)
    {
        $module = Module::make($module);
        $form = new Form(schema: $module->startFormSchema(), data: request()->all());
        app(WorkflowService::class)->start($module, $form);
    }
}
