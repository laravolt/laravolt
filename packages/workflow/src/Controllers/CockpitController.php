<?php

declare(strict_types=1);

namespace Laravolt\Workflow\Controllers;

use Illuminate\Routing\Controller;

class CockpitController extends Controller
{
    public function index()
    {
        $processDefinitionKeys = \Laravolt\Workflow\Models\Module::distinct('process_definition_key')->pluck('process_definition_key');

        return view('workflow::cockpit.index', compact('processDefinitionKeys'));
    }
}
