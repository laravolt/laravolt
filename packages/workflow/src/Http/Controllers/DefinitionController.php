<?php

namespace Laravolt\Workflow\Http\Controllers;

use Illuminate\Routing\Controller;
use Laravolt\Workflow\Models\ProcessDefinition;

class DefinitionController extends Controller
{
    public function index()
    {
        return view('laravolt::workflow.definition.index');
    }

    public function destroy(string $id)
    {
        $model = ProcessDefinition::findOrFail($id);
        $model->delete();

        return redirect()->back()->with('success', __('BPMN definition removed'));
    }
}
