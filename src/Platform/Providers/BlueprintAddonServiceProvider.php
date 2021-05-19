<?php

namespace Laravolt\Platform\Providers;

use Blueprint\Blueprint;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Laravolt\Platform\Services\LaravoltBlueprintGenerator;

class BlueprintAddonServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->extend(
            Blueprint::class,
            function (Blueprint $blueprint, Application $app) {
                $blueprint->registerGenerator(new LaravoltBlueprintGenerator());

                return $blueprint;
            }
        );
    }
}
