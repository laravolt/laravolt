<?php

declare(strict_types=1);

namespace Laravolt\Lookup;

use Illuminate\Support\Arr;
use Laravolt\Support\Base\BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    protected $name = 'lookup';

    public function boot()
    {
        parent::boot();
        $this->bootMenu();
    }

    protected function bootMenu()
    {
        if ($this->app->bound('laravolt.menu') && Arr::get($this->config, 'menu.enabled')) {
            $menu = app('laravolt.menu')->system;
            $group = $menu->add(__('Lookup'))
                ->data('icon', 'list')
                ->data('permission', config('laravolt.lookup.permission'));
            foreach (config('laravolt.lookup.collections') as $key => $collection) {
                $menu = $group->add($collection['label'], url("lookup/{$key}"))
                    ->active('lookup/' . $key . '/*');
                foreach ($collection['data'] ?? [] as $dataKey => $dataValue) {
                    $menu->data($dataKey, $dataValue);
                }
            }
        }

        return $this;
    }
}
