<?php

namespace Laravolt\Workflow;

use Laravolt\Support\Base\BaseServiceProvider;
use Laravolt\Workflow\Commands\WorkflowCheckCommand;
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
        $this->commands(WorkflowCheckCommand::class);
        
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
