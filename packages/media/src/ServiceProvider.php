<?php

namespace Laravolt\Media;

use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * Class PackageServiceProvider.
 *
 * @see http://laravel.com/docs/master/packages#service-providers
 * @see http://laravel.com/docs/master/providers
 */
class ServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        $this->bootRoutes()->bootMacro()->bootConfig();
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

    protected function bootConfig()
    {
        $this->publishes([
            __DIR__.'/../config/chunked-upload.php' => config_path('chunked-upload.php'),
        ], 'chunked-upload-config');

        return $this;
    }
}
