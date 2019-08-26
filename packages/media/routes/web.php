<?php

\Illuminate\Support\Facades\Route::group(
    [
        'prefix'     => 'media',
        'as'         => 'media::',
        'middleware' => ['web', 'auth'],
    ],
    function (\Illuminate\Routing\Router $router) {
        $router->post('/upload', \Laravolt\Media\Http\Controllers\Upload::class)->name('upload');
        $router->post('/remove', \Laravolt\Media\Http\Controllers\Remove::class)->name('remove');
    }
);
