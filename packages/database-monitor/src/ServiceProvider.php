<?php

namespace Laravolt\DatabaseMonitor;

use Laravolt\Support\Base\BaseServiceProvider;

/**
 * Class PackageServiceProvider.
 */
class ServiceProvider extends BaseServiceProvider
{
    public function getIdentifier()
    {
        return 'database-monitor';
    }

    protected function menu()
    {
        app('laravolt.menu.builder')->register(function ($menu) {
            $group = $menu->system->add(__('Database'))
                ->data('permission', \Laravolt\Platform\Enums\Permission::MANAGE_DB)
                ->data('icon', 'database');

            $group->add(__('Backup'), route('database-monitor::backup.index'))
                ->data('permission', \Laravolt\Platform\Enums\Permission::MANAGE_DB)
                ->active('database-monitor/backup')
                ->data('icon', 'database');
        });
    }
}
