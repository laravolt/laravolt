<?php

declare(strict_types=1);

Route::group(
    [
        'prefix' => config('laravolt.database-monitor.routes.prefix'),
        'as' => 'database-monitor::',
        'middleware' => [
            ...(array) config('laravolt.database-monitor.routes.middleware'),
            'can:'.Laravolt\Platform\Enums\Permission::MANAGE_DB,
        ],
    ],
    function () {
        Route::get('backup', [Laravolt\DatabaseMonitor\Controllers\BackupController::class, 'index'])
            ->name('backup.index');
        Route::post('backup', [Laravolt\DatabaseMonitor\Controllers\BackupController::class, 'store'])
            ->name('backup.store');
    }
);
