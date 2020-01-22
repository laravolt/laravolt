<?php

namespace Laravolt\Platform\Commands;

use Illuminate\Console\Command;

class AdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravolt:admin {name?} {email?} {password?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create admin user with wildcard (*) permission';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');
        $email = $this->argument('email');
        $password = $this->argument('password');

        if (!$name) {
            $name = $this->ask('Masukkan nama (display name):');
        }

        if (!$email) {
            $email = $this->ask('Masukkan email untuk login:');
        }

        if (!$password) {
            $password = $this->ask('Masukkan password:');
        }

        $role = app(config('laravolt.acl.models.role'))->whereHas('permissions', function ($permissions) {
            $permissions->whereName('*');
        })->first();

        if (!$role) {
            $role = app(config('laravolt.acl.models.role'))->create(['name' => 'admin']);
            $role->syncPermission(['*']);
        }

        $status = config('laravolt.auth.registration.status');
        if (config('laravolt.auth.activation.enable')) {
            $status = config('laravolt.auth.activation.status_after');
        }

        $user = app(config('auth.providers.users.model'))->updateOrCreate(
            [
                'email' => $email,
            ],
            [
                'name' => $name,
                'password' => bcrypt($password),
                'status' => $status,
            ]
        );

        $user->assignRole($role);

        $this->warn(str_repeat('-', 30));
        $this->warn('  User berhasil didaftarkan');
        $this->warn(str_repeat('-', 30));
        $this->table(null, [
            ['ID', $user->getKey()],
            ['Nama', $user->name],
            ['Email', $user->email],
            ['Password', $password],
            ['Role', $role->name],
            ['Status', $user->status],
        ]);
    }
}
