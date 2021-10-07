<x-volt-modal>

    <x-volt-panel title="Tambah {{ $config['label'] }}">
        {!! form()->post(route('auto-crud::resource.store', $resource)) !!}

        {!! form()->make($fields)->render() !!}

        {!! form()->action([
            form()->submit(__('Save')),
            form()->link(__('Cancel'), route('auto-crud::resource.index', $resource))
        ]) !!}


        {!! form()->close() !!}
    </x-volt-panel>

</x-volt-modal>
