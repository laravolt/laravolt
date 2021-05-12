<?php

use Laravolt\Workflow\Http\Controllers\DefinitionController;
use Laravolt\Workflow\Http\Controllers\InstancesController;
use Laravolt\Workflow\Http\Controllers\TaskController;
use Laravolt\Workflow\Http\Controllers\DefinitionXmlController;

Route::group(
    [
        'prefix'     => config('laravolt.workflow.routes.prefix'),
        'as'         => 'workflow::',
        'middleware' => config('laravolt.workflow.routes.middleware'),
    ],
    function () {
        Route::resource('definitions', DefinitionController::class);
        Route::get('definitions/{definition}/xml', DefinitionXmlController::class)->name('definitions.xml');
        Route::resource('module.instances', InstancesController::class);
        Route::resource('module.tasks', TaskController::class);
    }
);
