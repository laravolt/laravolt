<x-volt-app :title="$config['label']">
    <x-slot name="actions">
        <x-volt-link-button
                :label="__('Tambah')"
                url="{{ route('auto-crud::resource.create', $config['key']) }}"
                icon="plus">
        </x-volt-link-button>
    </x-slot>

    @livewire('laravolt::auto-crud.resource.table', ['resource' => $config])

</x-volt-app>
