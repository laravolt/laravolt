<?php

$router->group(
    [
        'prefix' => config('laravolt.lookup.route.prefix'),
        'as' => 'lookup::',
        'middleware' => config('laravolt.lookup.route.middleware'),
    ],
    function ($router) {
        $router->get('lookup/{collection}', [\Laravolt\Lookup\Controllers\LookupController::class, 'index'])
            ->name('lookup.index');
        $router->get('lookup/{collection}/create', [\Laravolt\Lookup\Controllers\LookupController::class, 'create'])
            ->name('lookup.create');
        $router->post('lookup/{collection}', [\Laravolt\Lookup\Controllers\LookupController::class, 'store'])
            ->name('lookup.store');
        $router->get('lookup/{lookup}/edit', [\Laravolt\Lookup\Controllers\LookupController::class, 'edit'])
            ->name('lookup.edit');
        $router->put('lookup/{lookup}', [\Laravolt\Lookup\Controllers\LookupController::class, 'update'])
            ->name('lookup.update');
        $router->delete('lookup/{lookup}', [\Laravolt\Lookup\Controllers\LookupController::class, 'destroy'])
            ->name('lookup.destroy');
    }
);
