<x-volt-app :title="$config['label']">
    <x-slot name="actions">
        <x-volt-button
                :label="__('Tambah')"
                url="{{ route('auto-crud::resource.create', $config['key']) }}"
                icon="plus"
                onclick="Livewire.emit('openModal', 'auto-crud.form.create', {{ json_encode(['resource' => $config['key']]) }})"
        >
        </x-volt-button>
    </x-slot>

    @livewire('volt-modal-bag')

    @livewire('auto-crud.table', ['resource' => $config])
</x-volt-app>
