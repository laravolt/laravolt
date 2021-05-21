<x-volt-app :title="$collection">
    <x-slot name="actions">
        <x-volt-backlink url="{{ route('lookup::lookup.index', $collection) }}">Kembali ke Index</x-volt-backlink>
    </x-slot>

    <x-volt-panel title="Edit {{ $config['label'] ?? $collection }}">
        {!! form()->bind($lookup)->put(route('lookup::lookup.update', $lookup)) !!}
        @include('lookup::lookup._form')
        {!! form()->close() !!}
    </x-volt-panel>


</x-volt-app>
