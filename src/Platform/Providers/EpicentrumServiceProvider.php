<?php

declare(strict_types=1);

namespace Laravolt\Platform\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Laravolt\Epicentrum\Contracts\Requests\Account\Delete;
use Laravolt\Epicentrum\Contracts\Requests\Account\Store;
use Laravolt\Epicentrum\Contracts\Requests\Account\Update;
use Laravolt\Epicentrum\Policies\UserPolicy;
use Laravolt\Platform\Enums\Permission;
use Laravolt\Platform\Models\User;
use Laravolt\Support\Contracts\TimezoneRepository;
use Livewire\Livewire;

use function Laravolt\platform_path;

/**
 * Class PackageServiceProvider.
 */
class EpicentrumServiceProvider extends BaseServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     *
     * @throws \Exception
     */
    public function register()
    {
        $this->mergeConfigFrom(platform_path('config/epicentrum.php'), 'laravolt.epicentrum');

        $this->app->bind(
            \Laravolt\Epicentrum\Repositories\RepositoryInterface::class,
            config('laravolt.epicentrum.repository.user')
        );

        $this->app->bind(
            \Laravolt\Epicentrum\Repositories\RoleRepositoryInterface::class,
            config('laravolt.epicentrum.repository.role')
        );

        $this->app->bind(
            TimezoneRepository::class,
            config('laravolt.epicentrum.repository.timezone')
        );

        $this->app->bind('laravolt.epicentrum.role', function () {
            return app(config('laravolt.epicentrum.models.role'));
        });

        $this->app->bind('laravolt.epicentrum.permission', function () {
            return app(config('laravolt.epicentrum.models.permission'));
        });

        $this->app->bind(Store::class, config('laravolt.epicentrum.requests.account.store'));
        $this->app->bind(Update::class, config('laravolt.epicentrum.requests.account.update'));
        $this->app->bind(Delete::class, config('laravolt.epicentrum.requests.account.delete'));

        Gate::policy(User::class, UserPolicy::class);
    }

    /**
     * Application is booting.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerBlade();
        /** @var \Illuminate\Foundation\Application */
        $app = $this->app;

        if ($app->bound('laravolt.acl')) {
            /** @var \Laravolt\Platform\Services\Acl */
            $acl = $this->app['laravolt.acl'];

            $acl->registerPermission(Permission::asArray());
        }

        if (! $app->routesAreCached() && (config('laravolt.platform.features.epicentrum', true))) {
            include platform_path('routes/epicentrum.php');
        }

        Livewire::component('volt-user-table', config('laravolt.epicentrum.table_view'));
    }

    protected function registerBlade()
    {
        Blade::directive('role', function ($expression) {
            return "<?php if(auth()->check() && auth()->user()->hasRole($expression)): ?>";
        });

        Blade::directive('endrole', function () {
            return '<?php endif; ?>';
        });
    }
}
