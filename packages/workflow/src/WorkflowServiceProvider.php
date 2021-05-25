<?php

namespace Laravolt\Workflow;

use Laravolt\Support\Base\BaseServiceProvider;
use Laravolt\Workflow\Livewire\DefinitionTable;
use Laravolt\Workflow\Livewire\ProcessInstancesTable;
use Livewire\Livewire;

class WorkflowServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        parent::boot();
        Livewire::component('laravolt::definition-table', DefinitionTable::class);
    }

    public function getIdentifier()
    {
        return 'workflow';
    }
}
