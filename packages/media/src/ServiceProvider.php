<?php

namespace Laravolt\Media;

use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Laravolt\Media\Console\CleanupStaleChunksCommand;

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
        $this->bootRoutes()->bootMacro()->bootCommands()->bootPublishing();
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

    protected function bootCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CleanupStaleChunksCommand::class,
            ]);
        }

        return $this;
    }

    protected function bootPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/chunked-upload.php' => config_path('chunked-upload.php'),
            ], 'laravolt-media-config');

            $this->publishes([
                __DIR__.'/../../resources/js/components/chunked-uploader.js' => public_path('js/components/chunked-uploader.js'),
            ], 'laravolt-media-assets');

            $this->publishes([
                __DIR__.'/../../resources/views/media/chunked-upload-examples.blade.php' => resource_path('views/media/chunked-upload-examples.blade.php'),
            ], 'laravolt-media-views');
        }

        return $this;
    }
}
