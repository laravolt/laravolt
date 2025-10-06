<?php

namespace Laravolt\Mailkeeper;

use Illuminate\Mail\MailServiceProvider;

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
    public function registerIlluminateMailer()
    {
        $this->registerConfig();

        if (config('laravolt.mailkeeper.enabled') === true) {
            $this->app->singleton(
                'mail.manager',
                function ($app) {
                    return new DbMailManager($app);
                }
            );

            $this->app->bind(
                'mailer',
                function ($app) {
                    return $app->make('mail.manager')->mailer();
                }
            );
        } else {
            parent::registerIlluminateMailer();
        }
    }

    /**
     * Application is booting.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            dirname(__DIR__).'/database/migrations' => database_path('migrations'),
        ], 'migrations');

        $this->registerCommands();
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
            $path,
            'laravolt.mailkeeper'
        );
    }
}
