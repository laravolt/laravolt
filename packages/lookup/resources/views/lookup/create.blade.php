@extends(config('laravolt.lookup.view.layout'))

@section('content')
    <x-titlebar title="Lookup">
        <x-backlink url="{{ route('lookup::lookup.index', $collection) }}">Kembali ke Index</x-backlink>
    </x-titlebar>

    <x-panel title="Tambah {{ $config['label'] ?? $collection }}">
        {!! form()->post(route('lookup::lookup.store', $collection)) !!}
        @include('lookup::lookup._form')
        {!! form()->close() !!}
    </x-panel>
@endsection
