<x-volt-public>
    <x-volt-panel :title="request('_title', __('Mulai Proses Baru'))" :icon="request('_icon', 'rocket')">
    {!! $module->startForm(route('workflow::form.store', ['module' => $module->id]), request()->all()) !!}
    </x-volt-panel>
</x-volt-public>
