<x-volt-app :title="$config['label'] ?? $collection">
    <x-slot name="actions">
        <x-volt-backlink url="{{ route('lookup::lookup.index', $collection) }}">Kembali ke Index
        </x-volt-backlink>
    </x-slot>

    <x-volt-panel title="Tambah {{ $config['label'] ?? $collection }}">
        {!! form()->post(route('lookup::lookup.store', $collection)) !!}
        @include('lookup::lookup._form')
        {!! form()->close() !!}
    </x-volt-panel>


</x-volt-app>
