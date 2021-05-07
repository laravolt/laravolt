<x-laravolt::layout.app title="{{ 'New '.$module->name }}">
    <x-slot name="actions">
        <x-laravolt::link-button
                :url="route('workflow::module.instances.index', $module->id)"
                icon="left arrow"
                :label="__('Back')"/>
    </x-slot>

</x-laravolt::layout.app>
