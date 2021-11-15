<?php

Route::group(
    [
        'prefix'     => config('laravolt.seo-editor.routes.prefix'),
        'as'         => 'seo-editor::',
        'middleware' => config('laravolt.seo-editor.routes.middleware'),
    ],
    function () {
        Route::resource('meta', \Laravolt\SeoEditor\Controllers\MetaController::class)
            ->only(['index', 'update'])
            ->names('meta');
    }
);
