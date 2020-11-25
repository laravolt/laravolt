<?php

Route::group(
    [
        'prefix' => config('laravolt.database-monitor.route.prefix'),
        'as' => 'database-monitor::',
        'middleware' => config('laravolt.database-monitor.route.middleware'),
    ],
    function () {
        Route::get('backup', [\Laravolt\DatabaseMonitor\Controllers\BackupController::class, 'index'])
            ->name('backup.index');
        Route::post('backup', [\Laravolt\DatabaseMonitor\Controllers\BackupController::class, 'store'])
            ->name('backup.store');
    }
);
