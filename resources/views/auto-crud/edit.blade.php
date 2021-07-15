<x-volt-app :title="$config['label']">
    <x-slot name="actions">
        <x-volt-backlink url="{{ route('auto-crud::resource.index', $config['key']) }}">Kembali ke Index
        </x-volt-backlink>
    </x-slot>

    <x-volt-panel title="Edit {{ $config['label'] }}">
        {!! form()->bind($model)->put(route('auto-crud::resource.update', [$config['key'], $model->getKey()])) !!}

        {!! form()->make($config['schema'])->render() !!}

        {!! form()->action([
            form()->submit(__('Save')),
            form()->link(__('Cancel'), route('auto-crud::resource.index', $config['key']))
        ]) !!}


        {!! form()->close() !!}
    </x-volt-panel>


</x-volt-app>
