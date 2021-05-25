<x-volt-app :title="$module->name">
    <x-slot name="actions">
        <x-volt-link-button
                :url="route('workflow::module.instances.create', [$module->id])"
                icon="plus"
                :label="__('New')"/>
    </x-slot>

    @livewire('laravolt::module-instances-table', ['variables' => $module->tableVariables]);

</x-volt-app>
