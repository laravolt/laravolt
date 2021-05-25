<?php

use Laravolt\Workflow\Http\Controllers\DefinitionController;
use Laravolt\Workflow\Http\Controllers\DefinitionXmlController;
use Laravolt\Workflow\Http\Controllers\EmbedFormController;
use Laravolt\Workflow\Http\Controllers\InstancesController;
use Laravolt\Workflow\Http\Controllers\TaskController;

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
        Route::resource('module.tasks', TaskController::class)->only('update');
    }
);

Route::group(
    [
        'prefix'     => config('laravolt.workflow.routes.prefix'),
        'as'         => 'workflow::',
    ],
    function () {
        Route::resource('embed-form', EmbedFormController::class)
            ->only(['show', 'store']);
    }
);
