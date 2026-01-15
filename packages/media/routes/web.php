<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Laravolt\Media\Controllers\ClientUploadController;

Route::group(
    [
        'prefix' => 'media',
        'as' => 'media::',
        'middleware' => config('laravolt.platform.middleware'),
    ],
    function (Illuminate\Routing\Router $router) {
        Route::post('media', [Laravolt\Media\Controllers\MediaController::class, 'store'])
            ->name('store')
            ->withoutMiddleware('auth');

        // Chunked upload routes (server-side)
        Route::post('chunk', function () {
            request()->merge(['handler' => 'chunked', '_action' => 'upload']);

            return app(Laravolt\Media\Controllers\MediaController::class)->store();
        })->name('chunk.upload')->withoutMiddleware('auth');

        Route::post('chunk/complete', function () {
            request()->merge(['handler' => 'chunked', '_action' => 'complete']);

            return app(Laravolt\Media\Controllers\MediaController::class)->store();
        })->name('chunk.complete')->withoutMiddleware('auth');

        Route::get('chunk/status', function () {
            request()->merge(['handler' => 'chunked', '_action' => 'status']);

            return app(Laravolt\Media\Controllers\MediaController::class)->store();
        })->name('chunk.status')->withoutMiddleware('auth');

        // Client-side upload routes (direct to R2/S3)
        Route::prefix('client-upload')->as('client-upload.')->group(function () {
            Route::get('config', [ClientUploadController::class, 'config'])
                ->name('config')
                ->withoutMiddleware('auth');

            Route::post('initiate', [ClientUploadController::class, 'initiate'])
                ->name('initiate')
                ->withoutMiddleware('auth');

            Route::post('presign-part', [ClientUploadController::class, 'presignPart'])
                ->name('presign-part')
                ->withoutMiddleware('auth');

            Route::post('presign-parts', [ClientUploadController::class, 'presignParts'])
                ->name('presign-parts')
                ->withoutMiddleware('auth');

            Route::post('complete-multipart', [ClientUploadController::class, 'completeMultipart'])
                ->name('complete-multipart')
                ->withoutMiddleware('auth');

            Route::post('complete-simple', [ClientUploadController::class, 'completeSimple'])
                ->name('complete-simple')
                ->withoutMiddleware('auth');

            Route::post('abort', [ClientUploadController::class, 'abort'])
                ->name('abort')
                ->withoutMiddleware('auth');
        });

        Route::delete('media/{id}', [Laravolt\Media\Controllers\MediaController::class, 'destroy'])->name('destroy')
            ->withoutMiddleware('auth');

        Route::get('stream/{media}', Laravolt\Media\Controllers\VideoStreamController::class)
            ->withoutMiddleware('auth')
            ->name('stream');
    }
);
