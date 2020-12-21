<?php

\Illuminate\Support\Facades\Route::group(
    [
        'namespace' => '',
        'prefix' => '',
        'as' => 'laravolt::',
        'middleware' => ['web'],
    ],
    function ($router) {
        $router->post('proxy', \Laravolt\SemanticForm\DbProxy::class)->name('proxy');
    }
);
