<?php

$router = app('router');
$router->group(
    [
        'namespace' => '\Laravolt\Epicentrum\Http\Controllers',
        'prefix' => config('laravolt.epicentrum.route.prefix'),
        'as' => 'epicentrum::',
        'middleware' => config('laravolt.epicentrum.route.middleware'),
    ],
    function ($router) {
        $router->get('/', ['uses' => 'DefaultController@index', 'as' => 'index']);

        // My Profile
        $router->get('my/profile', ['uses' => 'My\ProfileController@edit', 'as' => 'my.profile.edit']);
        $router->put('my/profile', ['uses' => 'My\ProfileController@update', 'as' => 'my.profile.update']);

        // My Password
        $router->get('my/password', ['uses' => 'My\PasswordController@edit', 'as' => 'my.password.edit']);
        $router->post('my/password', ['uses' => 'My\PasswordController@update', 'as' => 'my.password.update']);

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
