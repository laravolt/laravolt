<?php

declare(strict_types=1);

Route::group(
    [
        'namespace' => '\Laravolt\FileManager\Controllers',
        'prefix' => config('laravolt.file-manager.routes.prefix'),
        'as' => 'file-manager::',
        'middleware' => config('laravolt.file-manager.routes.middleware'),
    ],
    function () {
        Route::delete('{id}', ['uses' => 'FileController@destroy', 'as' => 'file.destroy']);
        Route::get('download', ['uses' => 'FileController@show', 'as' => 'file.download']);
    }
);
