<?php

namespace Laravolt\Asset;

use Laravolt\Support\Base\BaseServiceProvider;

class AssetServiceProvider extends BaseServiceProvider
{
    public function getIdentifier()
    {
        return 'asset';
    }

    protected function enabled()
    {
        return true;
    }

    public function boot()
    {
        $config = collect(config("laravolt.asset"));
        foreach ($config as $groupName => $groupConfig) {
            $this->bootAssetsManagerInstance($groupName, (array) $groupConfig);
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
    protected function bootAssetsManagerInstance($name, array $config): void
    {
        $this->app->singleton("laravolt.asset.group.$name", function () use ($config) {
            $config['public_dir'] ??= public_path();

            return new AssetManager($config);
        });
    }
}
