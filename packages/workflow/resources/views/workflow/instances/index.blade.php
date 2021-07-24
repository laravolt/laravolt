<x-volt-app :title="$module->name">
    <x-slot name="actions">
        <x-volt-link-button
                :url="route('workflow::module.instances.create', [$module->id])"
                icon="plus"
                :label="__('New')"/>
    </x-slot>

    @if($module->table === \Laravolt\Workflow\Livewire\ProcessInstancesTable::class)
        @livewire('laravolt::instances-table', ['moduleId' => $module->id])
    @else
        @livewire($module->table)
    @endif

</x-volt-app>
