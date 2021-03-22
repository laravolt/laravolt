<?php

Route::group(
    [
        'namespace'  => '\Laravolt\Epilog\Http\Controllers',
        'prefix'     => config('laravolt.epilog.routes.prefix'),
        'as'         => 'epilog::',
        'middleware' => config('laravolt.epilog.routes.middleware'),
    ],
    function () {
        Route::resource('/', 'LogController', ['only' => ['index']])->names('log');
    }
);
