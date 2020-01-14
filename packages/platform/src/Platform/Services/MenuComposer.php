<?php

declare(strict_types=1);

namespace Laravolt\Platform\Services;

use Illuminate\Contracts\View\View;
use Laravolt\Platform\Enums\Permission;

class MenuComposer
{
    public function compose(View $view)
    {
        if (config('laravolt.epicentrum.menu.enable')) {
            $this->epicentrumMenu();
        }
    }

    protected function epicentrumMenu()
    {
        $menu = app('laravolt.menu')->system;
        $menu->add(trans('laravolt::label.users'), route('epicentrum::users.index'))
            ->data('icon', 'users')
            ->data('permission', Permission::MANAGE_USER)
            ->active(config('laravolt.epicentrum.route.prefix').'/users/*');

        $menu->add(trans('laravolt::label.roles'), route('epicentrum::roles.index'))
            ->data('icon', 'mask')
            ->data('permission', Permission::MANAGE_ROLE)
            ->active(config('laravolt.epicentrum.route.prefix').'/roles/*');

        $menu->add(trans('laravolt::label.permissions'), route('epicentrum::permissions.edit'))
            ->data('icon', 'shield')
            ->data('permission', Permission::MANAGE_PERMISSION)
            ->active(config('laravolt.epicentrum.route.prefix').'/permissions/*');
    }
}
