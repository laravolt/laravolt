<?php

namespace Laravolt\Mailkeeper;

use Illuminate\Mail\MailServiceProvider;
use Swift_Mailer;

/**
 * Class PackageServiceProvider.
 */
class ServiceProvider extends MailServiceProvider
{
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function registerSwiftMailer()
    {
        $this->registerConfig();

        if (config('laravolt.mailkeeper.enabled') === true) {
            $this->registerDbMailer();
        } else {
            parent::registerSwiftMailer();
        }
    }

    /**
     * Application is booting.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole() && config('laravolt.mailkeeper.migrations')) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }

        $this->publishes([
            realpath(__DIR__.'/../database/migrations') => database_path('migrations'),
        ], 'migrations');

        $this->registerCommands();
    }

    private function registerDbMailer()
    {
        $this->app->singleton('swift.mailer', function () {
            return new Swift_Mailer(new DbTransport());
        });
    }

    protected function registerCommands()
    {
        $this->commands(SendMailCommand::class);
    }

    protected function registerConfig()
    {
        $path = __DIR__.'/../config/mailkeeper.php';

        $this->publishes(
            [
                $path => config_path('laravolt/mailkeeper.php'),
            ]
        );

        $this->mergeConfigFrom(
            $path, 'laravolt.mailkeeper'
        );
    }
}
