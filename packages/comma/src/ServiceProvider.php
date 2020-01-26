<?php

namespace Laravolt\Comma;

use Laravolt\Support\Base\BaseServiceProvider;

/**
 * Class PackageServiceProvider.
 */
class ServiceProvider extends BaseServiceProvider
{
    public function getIdentifier()
    {
        return 'comma';
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        $this->app->singleton(
            'laravolt.comma',
            function () {
                return new Comma();
            }
        );

        $this->app->bind(
            'laravolt.comma.models.category',
            function () {
                return $this->app->make(config('laravolt.comma.models.category'));
            }
        );

        $this->app->bind(
            'laravolt.comma.models.post',
            function () {
                return $this->app->make(config('laravolt.comma.models.post'));
            }
        );

        $this->app->bind(
            'laravolt.comma.models.tag',
            function () {
                return $this->app->make(config('laravolt.comma.models.tag'));
            }
        );
    }

    /**
     * Application is booting.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        if (config('laravolt.comma.menu.enabled') && config('laravolt.comma.route.enabled')) {
            $this->registerMenu();
        }
    }

    protected function registerMenu()
    {
        if ($this->app->bound('laravolt.menu')) {
            $group = $this->app['laravolt.menu']->add('CMS');
            foreach (config('laravolt.comma.collections') as $key => $collection) {
                $menu = $group->add($collection['label'], route('comma::posts.index', ['collection' => $key]))
                    ->active('cms/posts/'.$key);
                foreach ($collection['data'] as $dataKey => $dataValue) {
                    $menu->data($dataKey, $dataValue);
                }
            }
        }
    }
}
