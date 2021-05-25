<x-volt-public>
    <x-volt-panel :title="__('Mulai Proses Baru')" icon="rocket">
    {!! $module->startForm(route('workflow::embed-form.store', ['_key' => $key]), request()->all()) !!}
    </x-volt-panel>
</x-volt-public>
