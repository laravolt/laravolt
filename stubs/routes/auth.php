<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\Logout;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'auth',
        'middleware' => 'guest',
    ],
    function (Router $router): void {
        $router->get('login', [LoginController::class, 'show'])->name('auth::login.show');
        $router->post('login', [LoginController::class, 'store'])->name('auth::login.store');

        $router->get('login-base', fn () => to_route('auth::login.show'))->name('login');

        // Password Reset Routes...
        $router->get('forgot', [ForgotPasswordController::class, 'show'])->name('auth::forgot.show');
        $router->post('forgot', [ForgotPasswordController::class, 'store'])->name('auth::forgot.store');
        $router->get('reset/{token}', [ResetPasswordController::class, 'show'])->name('auth::reset.show');
        $router->post('reset/{token}', [ResetPasswordController::class, 'store'])->name('auth::reset.store');

        if (config('laravolt.platform.features.registration')) {
            $router->get('register', [RegistrationController::class, 'show'])->name('auth::registration.show');
            $router->post('register', [RegistrationController::class, 'store'])->name('auth::registration.store');
        }
    }
);

Route::group(
    [
        'prefix' => 'auth',
        'middleware' => 'auth',
    ],
    function (Router $router): void {
        $router->any('logout', Logout::class)->name('auth::logout');

        if (config('laravolt.platform.features.verification')) {
            $router->get('/verify-email', [VerificationController::class, 'show'])
                ->name('verification.notice');

            $router->post('/verify-email', [VerificationController::class, 'store'])
                ->middleware(['throttle:6,1'])
                ->name('verification.send');

            $router->get('/verify-email/{id}/{hash}', [VerificationController::class, 'update'])
                ->middleware(['signed', 'throttle:6,1'])
                ->name('verification.verify');
        }
    }

);
