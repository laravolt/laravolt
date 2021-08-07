<x-volt-public>
    <x-volt-panel :title="request('_title', __('Mulai Proses Baru'))" :icon="request('_icon', 'rocket')">
    {!! $module->startForm(route('workflow::embed-form.store', ['_key' => $key]), request()->all()) !!}
    </x-volt-panel>
</x-volt-public>
