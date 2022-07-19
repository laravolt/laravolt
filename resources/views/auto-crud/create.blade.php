<x-volt-app :title="$config['label']">
    <x-slot name="actions">
        <x-volt-backlink url="{{ route('auto-crud::resource.index', $config['key']) }}">Kembali ke Index
        </x-volt-backlink>
    </x-slot>

    <x-volt-panel title="Tambah {{ $config['label'] }}">
        {!! form()->post(route('auto-crud::resource.store', $config['key'])) !!}

        {!! form()->make($fields)->bindValues(request()->old() + request()->all())->render() !!}

        {!! form()->action([
            form()->submit(__('Save')),
            form()->link(__('Cancel'), route('auto-crud::resource.index', $config['key']))
        ]) !!}


        {!! form()->close() !!}
    </x-volt-panel>


</x-volt-app>
