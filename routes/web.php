<?php

declare(strict_types=1);

/** @var Illuminate\Routing\Router $router */

use Laravolt\Platform\Controllers\DumpRequestController;

/** @var Illuminate\Support\Facades\Route $router */
$router = app('router');

$router->group(
    [
        'middleware' => config('laravolt.platform.middleware'),
        'prefix' => 'platform',
        'as' => 'platform::',
    ],
    function (Illuminate\Routing\Router $router) {
        $router->group(['prefix' => 'playground'], function (Illuminate\Routing\Router $router) {
            $router->view('ui', 'laravolt::playground.ui')->name('playground.ui');
            $router->view('form', 'laravolt::playground.form')->name('playground.form');
            $router->view('article', 'laravolt::playground.article')->name('playground.article');
        });

        // Component Showcase Routes
        $router->group(['prefix' => 'components', 'as' => 'components.'], function (Illuminate\Routing\Router $router) {
            $router->get('/', [Laravolt\Platform\Controllers\ComponentShowcaseController::class, 'index'])->name('index');
            $router->get('/{component}', [Laravolt\Platform\Controllers\ComponentShowcaseController::class, 'component'])->name('show');
        });
        $router->get('settings', [Laravolt\Platform\Controllers\SettingsController::class, 'edit'])->name('settings.edit');
        $router->put('settings', [Laravolt\Platform\Controllers\SettingsController::class, 'update'])->name('settings.update');

        $router->view('check', 'laravolt::platform.check')->name('check');
        $router->any('dump', DumpRequestController::class)->name('dump');
    }
);
