<?php

namespace Laravolt\Workflow\Http\Controllers;

use Illuminate\Contracts\View\View;
use Laravolt\Workflow\Models\ProcessInstance;
use Laravolt\Workflow\Values\Module;

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
        dd(request()->all());
    }
}
