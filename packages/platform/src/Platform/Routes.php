<?php

namespace Laravolt\Platform;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

class Routes
{
    public static function auth(array $options = []): void
    {
        Route::group(
            [
                'namespace' => '\Laravolt\Platform\Controllers',
                'middleware' => $options['middleware'] ?? [],
                'prefix' => $options['prefix'] ?? '',
                'as' => $options['as'] ?? '',
            ],
            function (Router $router) {
                // Authentication Routes...
                $router->get('login', 'LoginController@show')->name('login');
                $router->post('login', 'LoginController@store')->name('login.action');
                $router->any('logout', 'LoginController@destroy')->name('logout');

                // Password Reset Routes...
                $router->get('forgot', 'ForgotPasswordController@create')->name('forgot');
                $router->post('forgot', 'ForgotPasswordController@store')->name('forgot.action');
                $router->get('reset/{token}', 'ResetPasswordController@showResetForm')->name('reset');
                $router->post('reset/{token}', 'ResetPasswordController@reset')->name('reset.action');

                if (config('laravolt.auth.registration.enable')) {
                    // Registration Routes...
                    $router->get('register', 'RegisterController@showRegistrationForm')->name('register');
                    $router->post('register', 'RegisterController@register')->name('register.action');

                    // Activation Routes...
                    $router->get('activate/{token}', 'ActivationController@activate')->name('activate');
                }
            }
        );
    }
}
