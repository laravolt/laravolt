<?php

namespace Laravolt\Workflow\Policies;

use App\User;
use Illuminate\Support\Str;
use Laravolt\Workflow\Models\Module;

class ModulePolicy
{
    public function before($user, $ability)
    {
        if (config('laravolt.workflow.authorization.enabled') === false) {
            return true;
        }
    }

    public function view(User $user, Module $module)
    {
        return $user->roles->intersect($module->roles)->isNotEmpty();
    }

    public function create(User $user, Module $module)
    {
        $roles = $module->roles->intersect($user->roles)->filter(function ($item) {
            return Str::contains($item->pivot->permission, 'create');
        });

        return $roles->isNotEmpty();
    }

    public function edit(User $user, Module $module)
    {
        $roles = $module->roles->intersect($user->roles)->filter(function ($item) {
            return Str::contains($item->pivot->permission, 'edit');
        });

        return $roles->isNotEmpty();
    }

    public function delete(User $user, Module $module)
    {
        return false;
    }
}
