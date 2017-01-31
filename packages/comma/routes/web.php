<?php

Route::group(
    [
        'namespace'  => '\Laravolt\Comma\Http\Controllers',
        'prefix'     => config('laravolt.comma.route.prefix'),
        'as'         => 'comma::',
        'middleware' => config('laravolt.comma.route.middleware'),
    ],
    function () {

        Route::get('/', ['uses' => 'DefaultController@index', 'as' => 'index']);

        Route::resource('posts', 'PostController');
        Route::resource('categories', 'CategoryController');
    });
