<x-volt-app :title="$definition->present_title">
    <x-slot name="actions">
        <x-volt-link-button
                :url="route('workflow::definitions.index')"
                icon="left arrow"
                :label="__('Back')"/>
    </x-slot>

    @livewire('laravolt::instances-table')

</x-volt-app>
