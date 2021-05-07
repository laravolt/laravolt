<x-laravolt::layout.app title="{{ 'New '.$module->name }}">
    <x-slot name="actions">
        <x-laravolt::link-button
                :url="route('workflow::module.instances.index', $module->id)"
                icon="left arrow"
                :label="__('Back')"/>
    </x-slot>

    <x-laravolt::panel title="Mulai Proses Baru" icon="rocket">
        {!! form()->post(route('workflow::module.instances.store', $module->id))->multipart()->horizontal() !!}
        {!! $module->startForm() !!}
        {!! form()->action(form()->submit('Simpan')) !!}
        {!! form()->close() !!}
    </x-laravolt::panel>

</x-laravolt::layout.app>
