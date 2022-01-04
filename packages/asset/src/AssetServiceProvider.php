<?php

namespace Laravolt\Asset;

use Illuminate\Support\ServiceProvider;

class AssetServiceProvider extends ServiceProvider
{
    public function register()
    {
        $config = collect(config("laravolt.asset"));
        foreach ($config as $groupName => $groupConfig) {
            $this->registerAssetsManagerInstance($groupName, (array) $groupConfig);
        }
    }

    /**
     * Register an instance of the assets manager library in the IoC container.
     *
     * @param string $name   Name of the group
     * @param array  $config Config of the group
     *
     * @return void
     */
    protected function registerAssetsManagerInstance($name, array $config)
    {
        $this->app->singleton("laravolt.asset.group.$name", function () use ($config) {
            $config['public_dir'] ??= public_path();

            return new AssetManager($config);
        });
    }
}
