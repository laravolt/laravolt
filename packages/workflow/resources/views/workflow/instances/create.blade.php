<x-volt-app title="{{ 'New '.$module->name }}">
    <x-slot name="actions">
        <x-volt-link-button
                :url="route('workflow::module.instances.index', $module->id)"
                icon="left arrow"
                :label="__('Back')"/>
    </x-slot>

    <x-volt-panel :title="__('Mulai Proses Baru')" icon="rocket">
        {!! $module->startForm() !!}
    </x-volt-panel>

</x-volt-app>
