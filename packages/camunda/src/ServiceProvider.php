<?php

declare(strict_types=1);

namespace Laravolt\Camunda;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(realpath(__DIR__ . '/../config/camunda.php'), 'laravolt.camunda');
    }

    public function boot()
    {
    }
}
