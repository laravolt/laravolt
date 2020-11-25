<?php

Route::group(
    [
        'namespace' => '\Laravolt\FileManager\Controllers',
        'prefix' => config('laravolt.file-manager.route.prefix'),
        'as' => 'file-manager::',
        'middleware' => config('laravolt.file-manager.route.middleware'),
    ],
    function () {
        Route::delete('{id}', ['uses' => 'FileController@destroy', 'as' => 'file.destroy']);
        Route::get('download', ['uses' => 'FileController@download', 'as' => 'file.download']);
    }
);
