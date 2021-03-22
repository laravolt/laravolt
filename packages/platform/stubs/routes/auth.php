<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\Logout;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'auth',
    ],
    function (Router $router) {
        $router->get('login', [LoginController::class, 'show'])->name('auth::login.show');
        $router->post('login', [LoginController::class, 'store'])->name('auth::login.store');
        $router->any('logout', Logout::class)->name('auth::logout');

        // Password Reset Routes...
        $router->get('forgot', [ForgotPasswordController::class, 'show'])->name('auth::forgot.show');
        $router->post('forgot', [ForgotPasswordController::class, 'store'])->name('auth::forgot.store');
        $router->get('reset/{token}', [ResetPasswordController::class, 'show'])->name('auth::reset.show');
        $router->post('reset/{token}', [ResetPasswordController::class, 'store'])->name('auth::reset.store');

        if (config('laravolt.platform.features.registration')) {
            $router->get('register', [RegistrationController::class, 'show'])->name('auth::registration.show');
            $router->post('register', [RegistrationController::class, 'store'])->name('auth::registration.store');
        }

        if (config('laravolt.platform.features.verification')) {
            Route::get('/verify-email', [\App\Http\Controllers\Auth\VerificationController::class, 'show'])
                ->middleware('auth')
                ->name('verification.notice');

            Route::post('/verify-email', [\App\Http\Controllers\Auth\VerificationController::class, 'store'])
                ->middleware(['auth', 'throttle:6,1'])
                ->name('verification.send');

            Route::get('/verify-email/{id}/{hash}', [\App\Http\Controllers\Auth\VerificationController::class, 'update'])
                ->middleware(['auth', 'signed', 'throttle:6,1'])
                ->name('verification.verify');
        }
    }
);
