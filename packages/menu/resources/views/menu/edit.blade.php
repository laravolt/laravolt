@extends(config('laravolt.menu.view.layout'))

@section('content')

    <x-backlink url="{{ route('menu::menu.index') }}"></x-backlink>

    <x-panel title="Edit Menu">
        {!! form()->bind($menu)->put(route('menu::menu.update', $menu)) !!}
        @include('menu::menu._form')
        {!! form()->close() !!}
    </x-panel>

@endsection
