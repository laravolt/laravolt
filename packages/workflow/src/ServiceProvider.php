<?php

declare(strict_types=1);

namespace Laravolt\Camunda;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Laravolt\Camunda\Entities\Module;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(realpath(__DIR__ . '/../config/camunda.php'), 'laravolt.camunda');
        $this->mergeConfigFrom(realpath(__DIR__ . '/../config/workflow.php'), 'laravolt.workflow');

        $this->app->singleton('laravolt.workflow', \Laravolt\Camunda\Contracts\Workflow::class);
        $this->app->bind(\Laravolt\Camunda\Contracts\Workflow::class, function () {
            return new Workflow();
        });
    }

    public function boot()
    {
        $this->bootRoutes()
             ->bootMigrations()
             ->bootTranslations()
             ->bootViews()
             ->bootMacro()
             ->bindModule();
    }

    protected function bootRoutes()
    {
        if (config('laravolt.workflow.routes.enabled')) {
            $router = $this->app['router'];
            require __DIR__ . '/../routes/web.php';
        }

        return $this;
    }

    protected function bootTranslations()
    {
        $this->loadTranslationsFrom(realpath(__DIR__ . '/../resources/lang'), 'camunda');

        return $this;
    }

    protected function bootViews()
    {
        $this->loadViewsFrom(realpath(__DIR__ . '/../resources/views'), 'camunda');
        $this->loadViewsFrom(storage_path('surat-compiled'), 'surat-compiled');

        return $this;
    }

    protected function bootMacro()
    {
        Str::macro('humanize', function ($string) {
            return trim(preg_replace('/\s+/', ' ', Str::title(str_replace('_', ' ', $string))));
        });

        return $this;
    }

    protected function bindModule()
    {
        Route::bind('module', function ($id) {
            $module = config("workflow.modules.$id");
            $table = $module['table'] ?? null;

            if (! $module) {
                if (config('app.debug')) {
                    throw new \DomainException("File config config/modules/$id.php belum dibuat atau jalankan command `php artisan app:sync-module` terlebih dahulu untuk sinkronisasi Modul.");
                }
                abort(404);
            }

            $module['id'] = $module['id'] ?? $id;
            $module['action'] = $module['action'] ?? [];

            return Module::fromConfig($module);
        });

        return $this;
    }

    protected function bootMigrations()
    {
        $path = realpath(__DIR__ . '/../database/migrations');
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom($path);
        }
        $this->publishes([
            $path => database_path('migrations'),
        ], 'migrations');

        return $this;
    }
}
