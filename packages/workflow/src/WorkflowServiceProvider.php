<?php

namespace Laravolt\Workflow;

use Laravolt\Workflow\Livewire\DefinitionTable;
use Laravolt\Support\Base\BaseServiceProvider;
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
