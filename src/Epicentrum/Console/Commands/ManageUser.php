<?php

namespace Laravolt\Epicentrum\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Laravolt\Epicentrum\Repositories\RepositoryInterface;

class ManageUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravolt:manage-user {user?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'User management for superuser';

    protected $menu = [
        1 => 'Change Role',
        2 => 'Change Password',
    ];

    protected $repository;

    /**
     * ManageUser constructor.
     */
    public function __construct(RepositoryInterface $repository)
    {
        parent::__construct();
        $this->repository = $repository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $user = $this->validateUser($this->argument('user'));

        if ($user) {
            $this->chooseAction($user);
        }
    }

    protected function validateUser($identifier)
    {
        if (!$identifier) {
            $identifier = $this->ask('ID or email');
        }

        $user = app(config('auth.providers.users.model'))->find($identifier);

        if (!$user) {
            $user = app(config('auth.providers.users.model'))->whereEmail($identifier)->first();
        }

        if (!$user) {
            $this->warn('User not found');

            if ($this->confirm('Do you want to creaate new user?')) {
                $user = $this->repository->createByAdmin(
                    [
                        'email' => $this->ask('Email', $identifier),
                        'name' => $this->ask('Name', 'Fulan'),
                        'password' => $this->ask('Password', 'asdf1234'),
                    ]
                );
            }
        }

        return $user;
    }

    protected function chooseAction($user)
    {
        $message = sprintf('What do you want to do with user %s (ID: %s)', $user->email, $user->getKey());
        $action = Str::camel($this->choice($message, $this->menu));

        $this->{'action'.$action}($user);
    }

    protected function actionChangeRole($user)
    {
        $roles = app('laravolt.epicentrum.role')->pluck('name', 'id')->sortKeys();
        $options = (clone $roles)->prepend('all', 0);

        $selected = $this->choice('Type roles ID, separate by comma', $options->toArray(), null, null, true);

        if (collect($selected)->search('all') !== false) {
            $selected = $roles;
        }

        return $user->syncRoles($selected);
    }

    protected function actionChangePassword()
    {
    }
}
