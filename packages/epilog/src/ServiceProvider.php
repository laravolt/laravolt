<?php

namespace Laravolt\Epilog;

use Laravolt\Support\Base\BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function getIdentifier()
    {
        return 'epilog';
    }

    protected function menu()
    {
        app('laravolt.menu.sidebar')->register(function ($menu) {
            $menu->system
                ->add(__('Application Log'), route('epilog::log.index'))
                ->data('permission', \Laravolt\Platform\Enums\Permission::MANAGE_APPLICATION_LOG)
                ->active('epilog/*')
                ->data('icon', 'bug');
        });
    }
}
