<?php

\Illuminate\Support\Facades\Route::group(
    [
        'namespace' => '',
        'prefix' => '',
        'as' => 'laravolt::',
        'middleware' => ['web'],
    ],
    function ($router) {
        $router->post('laravolt/api/dropdown', \Laravolt\SemanticForm\DbProxy::class)->name('api.dropdown');
    }
);
