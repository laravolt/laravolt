<?php

namespace Laravolt\Asset;

use Laravolt\Support\Base\BaseServiceProvider;

class AssetServiceProvider extends BaseServiceProvider
{
    public function getIdentifier()
    {
        return 'asset';
    }

    public function register()
    {
        parent::register();

        $config = $this->config;
        foreach ($config as $groupName => $groupConfig) {
            $this->registerAssetsManagerInstance($groupName, (array)$groupConfig);
        }
    }

    /**
     * Register an instance of the assets manager library in the IoC container.
     *
     * @param  string  $name  Name of the group
     * @param  array  $config  Config of the group
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
