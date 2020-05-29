@extends(config('laravolt.menu.view.layout'))

@section('content')

    <x-backlink url="{{ route('menu::menu.index') }}"></x-backlink>

    <x-panel title="Tambah Menu">
        {!! form()->post(route('menu::menu.store')) !!}
        @include('menu::menu._form')
        {!! form()->close() !!}
    </x-panel>

@endsection
