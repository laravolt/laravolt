<?php

namespace Laravolt\Mailkeeper;

use Illuminate\Mail\MailServiceProvider;
use Swift_Mailer;

/**
 * Class PackageServiceProvider

 */
class ServiceProvider extends MailServiceProvider
{
    protected $defer = true;

    /**
     * Register the service provider.
     * @return void
     */
    public function registerSwiftMailer()
    {
        if ($this->app['config']['mail.driver'] === 'mailkeeper') {
            $this->registerDbMailer();
        } else {
            parent::registerSwiftMailer();
        }
    }

    /**
     * Application is booting
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

    }

    private function registerDbMailer() {
        $this->app->singleton('swift.mailer', function () {
            return new Swift_Mailer(new DbTransport());
        });
    }
}
