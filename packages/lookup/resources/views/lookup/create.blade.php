<x-laravolt::layout.app :title="$config['label'] ?? $collection">
    <x-slot name="actions">
        <x-laravolt::backlink url="{{ route('lookup::lookup.index', $collection) }}">Kembali ke Index
        </x-laravolt::backlink>
    </x-slot>

    <x-laravolt::panel title="Tambah {{ $config['label'] ?? $collection }}">
        {!! form()->post(route('lookup::lookup.store', $collection)) !!}
        @include('lookup::lookup._form')
        {!! form()->close() !!}
    </x-laravolt::panel>


</x-laravolt::layout.app>
