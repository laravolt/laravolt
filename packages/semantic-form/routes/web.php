<?php

\Illuminate\Support\Facades\Route::group(
    [
        'namespace' => '',
        'prefix' => '',
        'as' => 'laravolt::',
        'middleware' => ['web'],
    ],
    function ($router) {
        $router->get('proxy', \Laravolt\SemanticForm\DbProxy::class)->name('proxy');
    }
);
