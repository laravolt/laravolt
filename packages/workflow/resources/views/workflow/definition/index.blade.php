<x-laravolt::layout.app title="BPMN Definitions">
    <x-slot name="actions">
        <x-laravolt::link-button
                :url="route('workflow::definitions.create')"
                icon="plus"
                :label="__('laravolt::action.add')"/>
    </x-slot>

    @livewire('laravolt::user-table')

</x-laravolt::layout.app>
