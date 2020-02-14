<?php

use Laravolt\Workflow\Controllers\Actions\AutoSave;
use Laravolt\Workflow\Controllers\CockpitController;
use Laravolt\Workflow\Controllers\PrintController;
use Laravolt\Workflow\Controllers\ProcessController;
use Laravolt\Workflow\Controllers\TableController;
use Laravolt\Workflow\Controllers\TaskController;

$router->group(
    [
        'prefix' => config('laravolt.workflow.route.prefix'),
        'as' => 'workflow::',
        'middleware' => config('laravolt.workflow.route.middleware'),
    ],
    function ($router) {
        $router->get('module', [\Laravolt\Workflow\Controllers\ModuleController::class, 'index'])->name('module.index');
        $router->get('module/{id}/edit', [\Laravolt\Workflow\Controllers\ModuleController::class, 'edit'])->name('module.edit');
        $router->put('module/{id}', [\Laravolt\Workflow\Controllers\ModuleController::class, 'update'])->name('module.update');

        $router->get('table', [TableController::class, 'index'])->name('table.index');
        $router->get('cockpit', [CockpitController::class, 'index'])->name('cockpit.index');

        $router->get('{module}/{processInstanceId}/print/{templateId}', [PrintController::class, 'index'])->name('print.index');

        $router->get('{module}.bpmn', [ProcessController::class, 'bpmn'])->name('process.bpmn');
        $router->any('{module}.{format}', [ProcessController::class, 'report'])->name('process.report');
        $router->get('{module}', [ProcessController::class, 'index'])->name('process.index');
        $router->get('{module}/create', [ProcessController::class, 'create'])->name('process.create');
        $router->post('{module}', [ProcessController::class, 'store'])->name('process.store');

        $router->get('{module}/{id}', [ProcessController::class, 'show'])->name('process.show');

        if (config('laravolt.workflow.process_instance.editable')) {
            $router->get('{module}/{id}/edit', [ProcessController::class, 'edit'])->name('process.edit');
            $router->put('{module}/{id}', [ProcessController::class, 'update'])->name('process.update');
        }

        $router->delete('{module}/{id}', [ProcessController::class, 'destroy'])->name('process.destroy');

        $router->get('process-definition/{id}/xml', \Laravolt\Workflow\Controllers\ProcessDefinitionXmlController::class)->name('process-definition.xml');
        $router->get('process/{id}/xml', \Laravolt\Workflow\Controllers\ProcessXmlController::class)->name('process.xml');

        $router->post('{module}/task/{id}', [TaskController::class, 'store'])->name('task.store');
        $router->get('{module}/task/{id}/edit', [TaskController::class, 'edit'])->name('task.edit');
        $router->put('{module}/task/{id}', [TaskController::class, 'update'])->name('task.update');
        $router->put('{module}/task/{id}/autosave', AutoSave::class)->name('task.autosave');
    }
);

Route::group(['middleware' => ['web', 'auth']], function () {
    Route::get('managementcamunda/download', ['uses' => '\Laravolt\Workflow\Controllers\ManagementCamundaController@download'])->name('managementcamunda.download');
    Route::resource('managementcamunda', '\Laravolt\Workflow\Controllers\ManagementCamundaController');
    Route::resource('segment', \Laravolt\Workflow\Controllers\SegmentController::class);
    Route::get('getTasks', '\Laravolt\Workflow\Controllers\ManagementCamundaController@getTasks');
    Route::get('getFields', '\Laravolt\Workflow\Controllers\ManagementCamundaController@getFields');
    Route::get('getAttributes', '\Laravolt\Workflow\Controllers\ManagementCamundaController@getAttributes');
});
