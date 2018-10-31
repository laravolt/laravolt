<?php

Route::group(
    [
        'namespace'  => '\Laravolt\Epilog\Http\Controllers',
        'prefix'     => config('laravolt.epilog.route.prefix'),
        'as'         => 'epilog::',
        'middleware' => config('laravolt.epilog.route.middleware'),
    ],
    function () {

        Route::resource('backup', 'BackupController', ['only' => ['index']]);
        Route::resource('log', 'LogController', ['only' => ['index']]);
    });
