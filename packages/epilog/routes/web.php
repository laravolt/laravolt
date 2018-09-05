<?php

Route::group(
    [
        'namespace'  => '\Laravolt\Cockpit\Http\Controllers',
        'prefix'     => config('laravolt.cockpit.route.prefix'),
        'as'         => 'cockpit::',
        'middleware' => config('laravolt.cockpit.route.middleware'),
    ],
    function () {

        Route::resource('backup', 'BackupController', ['only' => ['index']]);
        Route::resource('log', 'LogController', ['only' => ['index']]);
    });
