<?php

namespace Laravolt\Workflow;

use Laravolt\Support\Base\BaseServiceProvider;
use Laravolt\Workflow\Commands\WorkflowCheckCommand;
use Laravolt\Workflow\Commands\SharWorkflowCreateCommand;
use Laravolt\Workflow\Commands\SharWorkflowListCommand;
use Laravolt\Workflow\Commands\SharWorkflowLaunchCommand;
use Laravolt\Workflow\Commands\SharWorkflowDeleteCommand;
use Laravolt\Workflow\Commands\SharWorkflowExportCommand;
use Laravolt\Workflow\Commands\SharInstanceListCommand;
use Laravolt\Workflow\Commands\SharInstanceShowCommand;
use Laravolt\Workflow\Commands\SharInstanceCompleteCommand;
use Laravolt\Workflow\Commands\SharSyncCommand;
use Laravolt\Workflow\Commands\SharHealthCommand;
use Laravolt\Workflow\Commands\SharStatisticsCommand;
use Laravolt\Workflow\Commands\SharMonitorCommand;
use Laravolt\Workflow\Commands\SharCleanupCommand;
use Laravolt\Workflow\Commands\SharBatchCommand;
use Laravolt\Workflow\Commands\SharSetupCommand;
use Laravolt\Workflow\Commands\SharHelpCommand;
use Laravolt\Workflow\Clients\SharClient;
use Laravolt\Workflow\Livewire\DefinitionTable;
use Laravolt\Workflow\Livewire\ProcessInstancesTable;
use Livewire\Livewire;

class WorkflowServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        parent::register();
        
        // Register SHAR client
        $this->app->singleton(SharClient::class, function ($app) {
            return new SharClient();
        });
        
        // Register SHAR workflow service
        $this->app->singleton(SharWorkflowService::class, function ($app) {
            return new SharWorkflowService($app->make(SharClient::class));
        });
    }

    public function boot()
    {
        parent::boot();
        Livewire::component('laravolt::definition-table', DefinitionTable::class);
        Livewire::component('laravolt::instances-table', ProcessInstancesTable::class);
        Livewire::component('laravolt::shar-workflow-table', \Laravolt\Workflow\Livewire\SharWorkflowTable::class);
        
        // Register workflow commands
        $this->commands([
            WorkflowCheckCommand::class,
            SharHelpCommand::class,
            SharSetupCommand::class,
            SharHealthCommand::class,
            SharStatisticsCommand::class,
            SharMonitorCommand::class,
            SharSyncCommand::class,
            SharCleanupCommand::class,
            SharBatchCommand::class,
            // Workflow management commands
            SharWorkflowCreateCommand::class,
            SharWorkflowListCommand::class,
            SharWorkflowLaunchCommand::class,
            SharWorkflowDeleteCommand::class,
            SharWorkflowExportCommand::class,
            // Instance management commands
            SharInstanceListCommand::class,
            SharInstanceShowCommand::class,
            SharInstanceCompleteCommand::class,
        ]);
        
        // Load SHAR routes if enabled
        if (config('workflow.shar.enabled')) {
            $this->loadRoutesFrom(__DIR__.'/../routes/shar.php');
        }
    }

    public function getIdentifier()
    {
        return 'workflow';
    }
}
