<?php

use Laravolt\Workflow\Http\Controllers\DefinitionController;
use Laravolt\Workflow\Http\Controllers\DefinitionXmlController;
use Laravolt\Workflow\Http\Controllers\EmbedFormController;
use Laravolt\Workflow\Http\Controllers\EmbedTrackingController;
use Laravolt\Workflow\Http\Controllers\InstancesController;
use Laravolt\Workflow\Http\Controllers\TaskController;

Route::group(
    [
        'prefix' => config('laravolt.workflow.routes.prefix'),
        'as' => 'workflow::',
        'middleware' => config('laravolt.workflow.routes.middleware'),
    ],
    function () {
        Route::resource('definitions', DefinitionController::class);
        Route::resource('module.instances', InstancesController::class)->withoutMiddleware('auth');
        Route::resource('module.tasks', TaskController::class)->only('update')->withoutMiddleware('auth');

        Route::get('definitions/{definition}/xml', DefinitionXmlController::class)
            ->name('definitions.xml')
            ->withoutMiddleware('auth');
        Route::resource('{module}/form', EmbedFormController::class)->only(['create', 'store'])
            ->withoutMiddleware('auth');
        Route::resource('{module}/tracker', EmbedTrackingController::class)->only(['index', 'show'])
            ->withoutMiddleware('auth');
    }
);
