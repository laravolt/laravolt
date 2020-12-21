<?php

\Illuminate\Support\Facades\Route::group(
    [
        'namespace' => '',
        'prefix' => '',
        'as' => 'laravolt::',
        'middleware' => ['web'],
    ],
    function ($router) {
        $router->any('proxy', \Laravolt\SemanticForm\DbProxy::class)->name('proxy');
    }
);
