<?php

declare(strict_types=1);

namespace Laravolt\Menu;

use Illuminate\Database\QueryException;
use Laravolt\Menu\Models\Menu;
use Laravolt\Support\Base\BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function getIdentifier()
    {
        return 'menu';
    }

    public function boot()
    {
        parent::boot();

        $this->loadMenuFromDatabase()
            ->bootMenu();
    }

    protected function bootMenu()
    {
        if (config('laravolt.menu.menu.enabled')) {
            app('laravolt.menu.sidebar')->register(function ($menu) {
                $menu->system
                    ->add(__('Menu Manager'), route('menu::menu.index'))
                    ->data('icon', 'bars')
                    ->data('permission', \Laravolt\Platform\Enums\Permission::MANAGE_MENU)
                    ->active(config('laravolt.menu.route.prefix').'/menu/*');
            });
        }

        return $this;
    }

    protected function loadMenuFromDatabase()
    {
        try {
            app('laravolt.menu.builder')->loadArray(Menu::toStructuredArray());
        } catch (QueryException $e) {
        }

        return $this;
    }
}
