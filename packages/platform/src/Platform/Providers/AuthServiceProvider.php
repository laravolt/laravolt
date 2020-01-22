<?php

declare(strict_types=1);

namespace Laravolt\Platform\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Laravolt\Contracts\ForgotPassword;

/**
 * Class PackageServiceProvider.
 *
 * @see     http://laravel.com/docs/master/packages#service-providers
 * @see     http://laravel.com/docs/master/providers
 */
class AuthServiceProvider extends BaseServiceProvider
{
    /**
     * Register the service provider.
     *
     * @see    http://laravel.com/docs/master/providers#the-register-method
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('laravolt.auth.registrar', function () {
            $class = config('laravolt.auth.registration.implementation');

            return new $class();
        });

        $this->app->bind('laravolt.auth.login', function () {
            $class = config('laravolt.auth.login.implementation');

            return new $class();
        });

        $this->app->bind('laravolt.auth.password.forgot', function () {
            $class = app(config('laravolt.auth.password.forgot.implementation'));
            if ($class instanceof ForgotPassword) {
                return $class;
            }

            throw new \InvalidArgumentException(
                sprintf('We expect %s instance, but you give %s.', ForgotPassword::class, get_class($class))
            );
        });

        $this->app->bind('laravolt.auth.password.reset', function () {
            $class = app(config('laravolt.auth.password.reset.implementation'));
            if ($class instanceof ForgotPassword) {
                return $class;
            }

            throw new \InvalidArgumentException(
                sprintf('We expect %s instance, but you give %s.', ForgotPassword::class, get_class($class))
            );
        });
    }

    /**
     * Application is booting.
     *
     * @see    http://laravel.com/docs/master/providers#the-boot-method
     *
     * @return void
     */
    public function boot()
    {
        if (!$this->app->routesAreCached()) {
            $this->bootRoutes();
        }

        if (config('laravolt.auth.captcha')) {
            $this->app->register('Anhskohbo\NoCaptcha\NoCaptchaServiceProvider');
        }
    }

    /**
     * Register the package routes.
     *
     * @warn   consider allowing routes to be disabled
     *
     * @see    http://laravel.com/docs/master/routing
     * @see    http://laravel.com/docs/master/packages#routing
     *
     * @return void
     */
    protected function bootRoutes()
    {
        $this->app['router']->group(
            [
                'namespace'  => '\Laravolt\Platform\Controllers',
                'middleware' => config('laravolt.auth.router.middleware'),
                'prefix'     => config('laravolt.auth.router.prefix'),
                'as'         => 'auth::',
            ],
            function (Router $router) {
                // Authentication Routes...
                $router->get('login', 'LoginController@showLoginForm')->name('login');
                $router->post('login', 'LoginController@login')->name('login');
                $router->any('logout', 'LoginController@logout')->name('logout');

                // Password Reset Routes...
                $router->get('forgot', 'ForgotPasswordController@create')->name('forgot');
                $router->post('forgot', 'ForgotPasswordController@store')->name('forgot');
                $router->get('reset/{token}', 'ResetPasswordController@showResetForm')->name('reset');
                $router->post('reset/{token}', 'ResetPasswordController@reset')->name('reset');

                if (config('laravolt.auth.registration.enable')) {
                    // Registration Routes...
                    $router->get('register', 'RegisterController@showRegistrationForm')->name('register');
                    $router->post('register', 'RegisterController@register')->name('register');

                    // Activation Routes...
                    $router->get('activate/{token}', 'ActivationController@activate')->name('activate');
                }

                if (config('laravolt.auth.cas.enable')) {
                    $router->get('cas/login', 'Cas\Login')->name('cas.login');
                    $router->post('cas/logout', 'Cas\Logout')->name('cas.logout');
                }
            }
        );
    }
}
