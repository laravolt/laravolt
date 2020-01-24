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

        Route::get('posts/{collection}', ['uses' => 'PostController@index', 'as' => 'posts.index']);
        Route::get('posts/{collection}/create', ['uses' => 'PostController@create', 'as' => 'posts.create']);
        Route::post('posts/{collection}', ['uses' => 'PostController@store', 'as' => 'posts.store']);
        Route::get('posts/{collection}/{id}/edit', ['uses' => 'PostController@edit', 'as' => 'posts.edit']);
        Route::put('posts/{id}', ['uses' => 'PostController@update', 'as' => 'posts.update']);
        Route::delete('posts/{id}', ['uses' => 'PostController@destroy', 'as' => 'posts.destroy']);
        Route::resource('media', 'MediaController', ['only' => ['index', 'store']]);
    });
