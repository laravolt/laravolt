<?php

declare(strict_types=1);

use Laravolt\AutoCrud\Controllers\ResourceController;

Illuminate\Support\Facades\Route::group(
    [
        'prefix' => config('laravolt.auto-crud.routes.prefix'),
        'as' => 'auto-crud::',
        'middleware' => config('laravolt.auto-crud.routes.middleware'),
    ],
    function (Illuminate\Routing\Router $router) {
        $router->get('{resource}', [ResourceController::class, 'index'])->name('resource.index');
        $router->get('{resource}/create', [ResourceController::class, 'create'])->name('resource.create');
        $router->post('{resource}', [ResourceController::class, 'store'])->name('resource.store');
        $router->get('{resource}/{id}', [ResourceController::class, 'show'])->name('resource.show');
        $router->get('{resource}/{id}/edit', [ResourceController::class, 'edit'])->name('resource.edit');
        $router->put('{resource}/{id}', [ResourceController::class, 'update'])->name('resource.update');
        $router->delete('{resource}/{id}', [ResourceController::class, 'destroy'])->name('resource.destroy');
    }
);
