<?php

declare(strict_types=1);

namespace Laravolt\Asset;

use Laravolt\Support\Base\BaseServiceProvider;

class AssetServiceProvider extends BaseServiceProvider
{
    public function getIdentifier()
    {
        return 'asset';
    }

    public function boot()
    {
        $config = collect(config('laravolt.asset'));
        foreach ($config as $groupName => $groupConfig) {
            $this->bootAssetsManagerInstance($groupName, (array) $groupConfig);
        }
    }

    protected function enabled()
    {
        return true;
    }

    /**
     * Register an instance of the assets manager library in the IoC container.
     *
     * @param  string  $name  Name of the group
     * @param  array  $config  Config of the group
     */
    protected function bootAssetsManagerInstance($name, array $config): void
    {
        $this->app->singleton("laravolt.asset.group.$name", function () use ($config) {
            $config['public_dir'] ??= public_path();

            return new AssetManager($config);
        });
    }
}
