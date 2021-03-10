<x-laravolt::layout.app :title="$title">
    <x-slot name="actions">
        <x-laravolt::backlink url="{{ route('lookup::lookup.index', $collection) }}">Kembali ke Index</x-laravolt::backlink>
    </x-slot>

    <x-laravolt::panel title="Edit {{ $config['label'] ?? $collection }}">
        {!! form()->bind($lookup)->put(route('lookup::lookup.update', $lookup)) !!}
        @include('lookup::lookup._form')
        {!! form()->close() !!}
    </x-laravolt::panel>


</x-laravolt::layout.app>
