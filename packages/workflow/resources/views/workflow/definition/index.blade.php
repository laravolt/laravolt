<x-volt-app title="BPMN Definitions">
    <x-slot name="actions">
        <x-volt-link-button
                :url="route('workflow::definitions.create')"
                icon="plus"
                :label="__('laravolt::action.add')"/>
    </x-slot>

    @livewire('laravolt::definition-table')

</x-volt-app>
