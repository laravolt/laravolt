<?php

namespace Laravolt\Workflow\Http\Controllers;

use Laravolt\Workflow\Entities\Module;

class EmbedFormController
{
    public function show(string $key)
    {
        $module = Module::make('rekrutmen');

        return view('laravolt::workflow.embed-form.show', compact('module', 'key'));
    }

    public function store()
    {
        dd(request()->all());
    }
}
