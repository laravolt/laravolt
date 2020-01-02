<?php

use Laravolt\Camunda\Controllers\Actions\AutoSave;
use Laravolt\Camunda\Controllers\PrintController;
use Laravolt\Camunda\Controllers\ProcessController;
use Laravolt\Camunda\Controllers\TableController;
use Laravolt\Camunda\Controllers\TaskController;

$router->group(
    [
        'prefix' => config('laravolt.workflow.routes.prefix'),
        'as' => 'camunda::',
        'middleware' => config('laravolt.workflow.routes.middleware'),
    ],
    function ($router) {
        $router->get('module', [\Laravolt\Camunda\Controllers\ModuleController::class, 'index'])->name('module.index');
        $router->get('module/{id}/edit', [\Laravolt\Camunda\Controllers\ModuleController::class, 'edit'])->name('module.edit');
        $router->put('module/{id}', [\Laravolt\Camunda\Controllers\ModuleController::class, 'update'])->name('module.update');

        $router->get('table', [TableController::class, 'index'])->name('table.index');

        $router->get('{module}/{processInstanceId}/print/{templateId}', [PrintController::class, 'index'])->name('print.index');

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

        $router->get('process/{id}/xml', \Laravolt\Camunda\Controllers\ProcessXmlController::class)->name('process.xml');

        $router->post('{module}/task/{id}', [TaskController::class, 'store'])->name('task.store');
        $router->get('{module}/task/{id}/edit', [TaskController::class, 'edit'])->name('task.edit');
        $router->put('{module}/task/{id}', [TaskController::class, 'update'])->name('task.update');
        $router->put('{module}/task/{id}/autosave', AutoSave::class)->name('task.autosave');
    }
);
