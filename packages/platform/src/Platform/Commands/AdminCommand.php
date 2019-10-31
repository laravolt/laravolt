<?php

namespace Laravolt\Platform\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class AdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravolt:admin {email} {password}';

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
        $email = $this->argument('email');
        $password = $this->argument('password');

        if (!$email) {
            $email = $this->ask('Masukkan email:');
        }

        if (!$password) {
            $password = $this->ask('Masukkan password:');
        }

        $role = app(config('laravolt.acl.models.role'))->firstOrCreate(['name' => 'admin']);
        $role->syncPermission(['*']);

        $status = config('laravolt.auth.registration.status');
        if (config('laravolt.auth.activation.enable')) {
            $status = config('laravolt.auth.activation.status_after');
        }

        $user = app(config('auth.providers.users.model'))->firstOrCreate(
            [
                'email' => $email,
            ],
            [
                'name' => Str::title(Str::before($email, '@')),
                'password' => bcrypt($password),
                'status' => $status,
            ]
        );

        $user->assignRole($role);
    }
}
