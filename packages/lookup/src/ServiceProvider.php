<?php

declare(strict_types=1);

namespace Laravolt\Lookup;

use Illuminate\Support\Arr;
use Laravolt\Support\Base\BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function getIdentifier()
    {
        return 'lookup';
    }

    public function boot()
    {
        parent::boot();
        $this->bootMenu();
    }

    protected function bootMenu()
    {
        if (Arr::get($this->config, 'menu.enabled')) {
            app('laravolt.menu.sidebar')->register(function ($menu) {
                $menu = $menu->system;
                $group = $menu->add(__('Lookup'))
                    ->data('icon', 'list')
                    ->data('permission', config('laravolt.lookup.permission'));

                foreach (config('laravolt.lookup.collections') as $key => $collection) {
                    $menu = $group->add($collection['label'], url("lookup/{$key}"))
                        ->active('lookup/'.$key.'/*');
                    foreach ($collection['data'] ?? [] as $dataKey => $dataValue) {
                        $menu->data($dataKey, $dataValue);
                    }
                }
            });
        }

        return $this;
    }
}
