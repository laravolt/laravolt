<?php

\Illuminate\Support\Facades\Route::group(
    [
        'prefix'     => 'media',
        'as'         => 'media::',
        'middleware' => ['web', 'auth'],
    ],
    function (\Illuminate\Routing\Router $router) {
        Route::post('upload', [\Laravolt\Media\Controllers\MediaController::class, 'store'])
            ->name('store');
    }
);
