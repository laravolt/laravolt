<?php

use Laravolt\Workflow\Http\Controllers\DefinitionController;

Route::group(
    [
        'prefix'     => config('laravolt.workflow.routes.prefix'),
        'as'         => 'workflow::',
        'middleware' => config('laravolt.workflow.routes.middleware'),
    ],
    function () {
        Route::resource('definitions', DefinitionController::class);
    }
);
