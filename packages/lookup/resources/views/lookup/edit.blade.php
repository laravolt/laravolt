@extends(config('laravolt.lookup.view.layout'))

@section('content')
    <x-titlebar title="Lookup">
        <x-backlink url="{{ route('lookup::lookup.index', $collection) }}">Kembali ke Index</x-backlink>
    </x-titlebar>

    <x-panel title="Edit {{ $config['label'] ?? $collection }}">
        {!! form()->bind($lookup)->put(route('lookup::lookup.update', $lookup)) !!}
        @include('lookup::lookup._form')
        {!! form()->close() !!}
    </x-panel>

@endsection
