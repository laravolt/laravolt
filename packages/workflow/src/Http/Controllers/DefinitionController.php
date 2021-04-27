<?php

namespace Laravolt\Workflow\Http\Controllers;

use Illuminate\Routing\Controller;

class DefinitionController extends Controller
{
    public function index()
    {
        return view('laravolt::workflow.definition.index');
    }
}
