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

    @push('script')
        <script>
            function submit() {

                console.log('submit');
                // fetch('/contact', {
                //     method: 'POST',
                //     headers: {'Content-Type': 'application/json'},
                //     body: JSON.stringify(this.formData)
                // })
                //     .then(() => {
                //         this.message = 'Form sucessfully submitted!'
                //     })
                //     .catch(() => {
                //         this.message = 'Ooops! Something went wrong!'
                //     })
                return false;
            }
        </script>
    @endpush
</x-volt-app>
