<?php

/** @var \Illuminate\Routing\Router $router */

use Laravolt\Platform\Controllers\DumpRequestController;

/** @var \Illuminate\Support\Facades\Route $router */
$router = app('router');

$router->group(
    [
        'middleware' => ['web', 'auth'],
        'prefix' => 'platform',
        'as' => 'platform::',
    ],
    function (\Illuminate\Routing\Router $router) {
        $router->group(['prefix' => 'playground'], function (\Illuminate\Routing\Router $router) {
            $router->view('ui', 'laravolt::playground.ui')->name('playground.ui');
            $router->view('form', 'laravolt::playground.form')->name('playground.form');
            $router->view('article', 'laravolt::playground.article')->name('playground.article');
        });
        $router->get('settings', [\Laravolt\Platform\Controllers\SettingsController::class, 'edit'])->name('settings.edit');
        $router->put('settings', [\Laravolt\Platform\Controllers\SettingsController::class, 'update'])->name('settings.update');

        $router->view('check', 'laravolt::platform.check')->name('check');
        $router->any('dump', DumpRequestController::class)->name('dump');
    }
);

$router->group(
    [
        'namespace' => '\Laravolt\Epicentrum\Http\Controllers',
        'prefix' => config('laravolt.epicentrum.routes.prefix'),
        'as' => 'epicentrum::',
        'middleware' => config('laravolt.epicentrum.routes.middleware'),
    ],
    function ($router) {
        $router->get('/', ['uses' => 'DefaultController@index', 'as' => 'index']);

        $router
            ->namespace('User')
            ->middleware('can:'.\Laravolt\Platform\Enums\Permission::MANAGE_USER)
            ->group(function ($router) {
                $router->resource('users', 'UserController');
                $router->resource('account', 'AccountController')->only('edit', 'update');
                $router->resource('password', 'Password\\PasswordController')->only('edit');
                $router->post('password/{id}/reset', 'Password\\Reset')->name('password.reset');
                $router->post('password/{id}/generate', 'Password\\Generate')->name('password.generate');
            });

        $router
            ->middleware('can:'.\Laravolt\Platform\Enums\Permission::MANAGE_ROLE)
            ->resource('roles', 'RoleController');

        $router
            ->middleware('can:'.\Laravolt\Platform\Enums\Permission::MANAGE_PERMISSION)
            ->group(function ($router) {
                $router->get('permissions', ['uses' => 'PermissionController@edit', 'as' => 'permissions.edit']);
                $router->put('permissions', ['uses' => 'PermissionController@update', 'as' => 'permissions.update']);
            });
    }
);
