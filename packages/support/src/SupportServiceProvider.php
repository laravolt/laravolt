<?php namespace Laravolt\Support;


use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class SupportServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerBladeExtensions();
        $this->registerTranslations();
        $this->registerConfigurations();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register('Krucas\Notification\NotificationServiceProvider');
        AliasLoader::getInstance()->alias('Notification', 'Krucas\Notification\Facades\Notification');
    }

    /**
     * Register Blade extensions.
     *
     * @return void
     */
    protected function registerBladeExtensions()
    {

        $blade = $this->app['view']->getEngineResolver()->resolve('blade')->getCompiler();

        $blade->directive('sortby', function ($expression)
        {
            return "<?php echo \Laravolt\Support\Pagination\Sortable::link(array {$expression});?>";
        });

    }

    /**
     * Register the package translations
     *
     * @see http://laravel.com/docs/5.1/packages#translations
     * @return void
     */
    protected function registerTranslations()
    {
        $this->loadTranslationsFrom($this->packagePath('resources/lang'), 'support');
    }

    /**
     * Register the package configurations
     *
     * @see http://laravel.com/docs/5.1/packages#configuration
     * @return void
     */
    protected function registerConfigurations()
    {
        $this->mergeConfigFrom(
            $this->packagePath('resources/config/timezones.php'), 'timezones'
        );
    }

    /**
     * Loads a path relative to the package base directory
     *
     * @param string $path
     * @return string
     */
    protected function packagePath($path = '')
    {
        return sprintf("%s/../%s", __DIR__ , $path);
    }

}
