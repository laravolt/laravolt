<x-volt-app :title="$config['label']">
    <x-slot name="actions">
        <x-volt-backlink url="{{ route('auto-crud::resource.index', $config['key']) }}">Kembali ke Index
        </x-volt-backlink>
    </x-slot>

    <x-volt-panel title="Detail {{ $config['label'] }} #{{ $model->getKey() }}">
        {!! form()->make($fields)->bindValues($model->toArray())->display() !!}
    </x-volt-panel>


</x-volt-app>
