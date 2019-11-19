<?php

namespace Laravolt\Media;

use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * Class PackageServiceProvider
 *
 * @package Laravolt\Ui
 * @see http://laravel.com/docs/master/packages#service-providers
 * @see http://laravel.com/docs/master/providers
 */
class ServiceProvider extends BaseServiceProvider
{
    public function register()
    {
    }

    public function boot()
    {
        $this->bootRoutes();
    }

    protected function bootRoutes()
    {
        require __DIR__.'/../routes/web.php';

        return $this;
    }

    protected function bootMacro()
    {
        Request::macro('media', function ($key) {
            return new MediaInputBag($key);
        });

        return $this;
    }
}
