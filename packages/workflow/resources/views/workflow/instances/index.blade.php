<x-laravolt::layout.app :title="$module->name">
    <x-slot name="actions">
        <x-laravolt::link-button
                :url="route('workflow::module.instances.create', [$module->id])"
                icon="plus"
                :label="__('New')"/>
    </x-slot>

    <livewire:tables.applicant-tables />

</x-laravolt::layout.app>
