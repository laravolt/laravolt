<?php

declare(strict_types=1);

namespace Laravolt\Workflow;

use Illuminate\Support\Facades\Route;
use Laravolt\Support\Base\BaseServiceProvider;
use Laravolt\Workflow\Console\Commands\DeployCommand;
use Laravolt\Workflow\Console\Commands\Import;
use Laravolt\Workflow\Console\Commands\MakeCommand;
use Laravolt\Workflow\Console\Commands\ResetTransaction;
use Laravolt\Workflow\Console\Commands\SyncModule;
use Laravolt\Workflow\Entities\Module;

class ServiceProvider extends BaseServiceProvider
{
    public function getIdentifier()
    {
        return 'workflow';
    }

    public function register()
    {
        parent::register();

        $this->app->singleton('laravolt.workflow', \Laravolt\Workflow\Contracts\Workflow::class);
        $this->app->bind(\Laravolt\Workflow\Contracts\Workflow::class, function () {
            return new Workflow();
        });

        $this->commands([
            DeployCommand::class,
            Import::class,
            MakeCommand::class,
            SyncModule::class,
            ResetTransaction::class,
        ]);
    }

    public function boot()
    {
        parent::boot();

        $this->bootMenu()
            ->bindModule();
    }

    protected function bootMenu()
    {
        if (config('laravolt.workflow.menu.enabled')) {
            app('laravolt.menu.sidebar')->register(function ($menu) {
                $menu = $menu->system->add('Workflow')->data('icon', 'fork');
                $menu->add('Module', route('workflow::module.index'))->active('workflow/module');
                $menu->add('Cockpit', route('workflow::cockpit.index'))->active('workflow/cockpit');
                $menu->add('Form Fields', route('managementcamunda.index'))
                    ->data('icon', 'wpforms')
                    ->active('managementcamunda/*');

                $menu->add('Segment', route('segment.index'))
                    ->data('icon', 'wpforms')
                    ->active('segment/*');
            });
        }

        return $this;
    }

    protected function bootViews()
    {
        parent::bootViews();
        $this->loadViewsFrom(realpath(__DIR__.'/../resources/views/managementcamunda'), 'managementcamunda');
        $this->loadViewsFrom(storage_path('surat-compiled'), 'surat-compiled');

        return $this;
    }

    protected function bindModule()
    {
        Route::bind('module', function ($id) {
            $module = config("workflow-modules.$id");
            $table = $module['table'] ?? null;

            if (!$module) {
                if (config('app.debug')) {
                    throw new \DomainException("File config config/workflow-modules/$id.php belum dibuat atau jalankan command `php artisan app:sync-module` terlebih dahulu untuk sinkronisasi Modul.");
                }
                abort(404);
            }

            $module['id'] = $module['id'] ?? $id;
            $module['action'] = $module['action'] ?? [];

            return Module::fromConfig($module);
        });

        return $this;
    }
}
