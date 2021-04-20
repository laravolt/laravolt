<?php

namespace Laravolt\Epicentrum\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ManageRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravolt:manage-role {role?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Role management for superuser';

    protected $menu = [
        1 => 'Change Permission',
    ];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $role = $this->validateRole($this->argument('role'));

        if ($role) {
            $this->chooseAction($role);
        }
    }

    protected function validateRole($identifier)
    {
        if (!$identifier) {
            $identifier = $this->ask('ID or role name');
        }

        $role = app('laravolt.epicentrum.role')->find($identifier);

        if (!$role) {
            $role = app('laravolt.epicentrum.role')->whereName($identifier)->first();
        }

        if (!$role) {
            $createNew = $this->confirm('Role not found, do you want to create a new role named '.$identifier);

            if ($createNew) {
                $role = app('laravolt.epicentrum.role')->create(['name' => $identifier]);

                if ($role) {
                    $this->info("Role {$role->name} successfully created");
                }
            }
        }

        return $role;
    }

    protected function chooseAction($role)
    {
        $message = sprintf('What do you want to do with role %s (ID: %s)', $role->name, $role->getKey());
        $action = Str::camel($this->choice($message, $this->menu));

        $this->{'action'.$action}($role);
    }

    protected function actionChangePermission($role)
    {
        $permissions = app('laravolt.epicentrum.permission')->pluck('name', 'id')->sortKeys();
        $options = (clone $permissions)->prepend('all', 0);

        $selected = $this->choice('Type permission ID, separate by comma', $options->toArray(), null, null, true);

        if (collect($selected)->contains(0)) {
            $selected = $permissions->toArray();
        }

        return $role->syncPermission($selected);
    }

    protected function actionChangePassword()
    {
    }
}
