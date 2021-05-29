<?php

namespace Laravolt\Workflow;

use Laravolt\Support\Base\BaseServiceProvider;
use Laravolt\Workflow\Commands\WorkflowCheckCommand;
use Laravolt\Workflow\Livewire\DefinitionTable;
use Laravolt\Workflow\Livewire\ProcessInstancesTable;
use Livewire\Livewire;

class WorkflowServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        parent::boot();
        Livewire::component('laravolt::definition-table', DefinitionTable::class);
        Livewire::component('laravolt::instances-table', ProcessInstancesTable::class);
        $this->commands(WorkflowCheckCommand::class);
    }

    public function getIdentifier()
    {
        return 'workflow';
    }

    public function menu()
    {
        $menu = [
            'Workflow' => [
                'order' => 98,
                'menu' => [
                    'BPMN' => [
                        'route' => 'workflow::definitions.index',
                        'active' => 'workflow/definitions/*',
                        'icon' => 'code-branch',
                        'permissions' => [\Laravolt\Platform\Enums\Permission::MANAGE_WORKFLOW],
                    ],
                ],
            ],
        ];

        $this->app['laravolt.menu.builder']->loadArray($menu);
    }
}
