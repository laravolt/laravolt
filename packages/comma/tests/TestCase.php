<?php

namespace Laravolt\Comma\Tests;

use Illuminate\Database\Schema\Blueprint;
use Laravolt\Comma\Tests\Dummy\User;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getDatabasePath()
    {
        return ':memory:';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadLaravelMigrations();
        $this->artisan('migrate');
    }

    protected function getPackageProviders($app)
    {
        return [
            \Laravolt\Comma\ServiceProvider::class,
            \Orchestra\Database\ConsoleServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver'   => 'sqlite',
            'database' => $this->getDatabasePath(),
            'prefix'   => '',
        ]);

        $app['config']->set('auth.providers.users.model', User::class);
        $app['config']->set('session.expire_on_close', false);
    }

    protected function setUpDatabase()
    {
        $this->createUserTable();

        $this->loadMigrationsFrom([
            '--database' => 'sqlite',
            '--realpath' => realpath(__DIR__.'/../database/migrations'),
        ]);

        $this->beforeApplicationDestroyed(function () {
            $this->cleanDatabase();
        });
    }

    protected function cleanDatabase()
    {
    }

    protected function createUserTable()
    {
        $this->app['db']->connection()->getSchemaBuilder()->create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('status')->default('ACTIVE');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    protected function createUser()
    {
        return User::create(['name' => 'Adnan', 'email' => 'adnan@gmail.com', 'password' => bcrypt('password')]);
    }
}
