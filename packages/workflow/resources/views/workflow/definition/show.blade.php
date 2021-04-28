<x-laravolt::layout.app :title="$definition->present_title">
    <x-slot name="actions">
        <x-laravolt::link-button
                :url="route('workflow::definitions.index')"
                icon="left arrow"
                :label="__('Back')"/>
    </x-slot>

</x-laravolt::layout.app>
