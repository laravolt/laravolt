@extends(config('laravolt.epicentrum.view.layout'))

@section('content')

    <x-laravolt::titlebar :title="__('laravolt::label.users')">
        <x-laravolt::link-button url="{{ route('epicentrum::users.create') }}"
                                 icon="plus"
                                 label="{{ __('laravolt::action.add') }}"/>
    </x-laravolt::titlebar>

    {!! $table !!}
@endsection
