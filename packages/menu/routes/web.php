<?php

use Laravolt\Menu\Controllers\MenuController;

$router->group(
    [
        'prefix' => config('laravolt.menu.route.prefix'),
        'as' => 'menu::',
        'middleware' => config('laravolt.menu.route.middleware'),
    ],
    function ($router) {
        $router->get('menu', [MenuController::class, 'index'])->name('menu.index');
        $router->get('menu/download', [MenuController::class, 'download'])->name('menu.download');
        $router->get('menu/create', [MenuController::class, 'create'])->name('menu.create');
        $router->post('menu', [MenuController::class, 'store'])->name('menu.store');
        $router->get('menu/{menu}/edit', [MenuController::class, 'edit'])->name('menu.edit');
        $router->put('menu/{menu}', [MenuController::class, 'update'])->name('menu.update');
        $router->delete('menu/{menu}', [MenuController::class, 'destroy'])->name('menu.destroy');
    }
);
