<?php

use Illuminate\Support\Facades\Route;
use Laravolt\Epicentrum\Http\Controllers\DefaultController;
use Laravolt\Epicentrum\Http\Controllers\PermissionController;
use Laravolt\Epicentrum\Http\Controllers\RoleController;
use Laravolt\Epicentrum\Http\Controllers\User\AccountController;
use Laravolt\Epicentrum\Http\Controllers\User\Password\Generate;
use Laravolt\Epicentrum\Http\Controllers\User\Password\PasswordController;
use Laravolt\Epicentrum\Http\Controllers\User\Password\Reset;
use Laravolt\Epicentrum\Http\Controllers\User\UserController;

Route::group(
    [
        'prefix' => 'epicentrum',
        'as' => 'epicentrum::',
        'middleware' => config('laravolt.platform.middleware'),
    ],
    function () {
        Route::get('/', [DefaultController::class, 'index'])->name('index');

        Route::resource('users', UserController::class)->except('show');
        Route::resource('account', AccountController::class)->only('edit', 'update');
        Route::resource('password', PasswordController::class)->only('edit');
        Route::post('password/{id}/reset', Reset::class)->name('password.reset');
        Route::post('password/{id}/generate', Generate::class)->name('password.generate');

        Route::resource('roles', RoleController::class);

        Route::get('permissions', [PermissionController::class, 'edit'])->name('permissions.edit');
        Route::put('permissions', [PermissionController::class, 'update'])->name('permissions.update');
    }
);
