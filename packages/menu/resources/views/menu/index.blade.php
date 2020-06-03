@extends(config('laravolt.menu.view.layout'))

@section('content')

    <x-titlebar title="{{ __('Menu') }}">
        <x-item>
            <x-link url="{{ route('menu::menu.create') }}" icon="plus circle">{{ __('Tambah') }}</x-link>
            <x-link url="{{ route('menu::menu.download') }}" icon="download" class="icon"></x-link>
        </x-item>
    </x-titlebar>

    {!! $table !!}
@endsection
