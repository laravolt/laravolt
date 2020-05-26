@extends(config('laravolt.epicentrum.view.layout'))

@section('content')

    <x-titlebar :title="__('laravolt::label.users')">
        <div class="item">
            <x-link url="{{ route('epicentrum::users.create') }}">
                <i class="icon plus"></i> @lang('laravolt::action.add')
            </x-link>
        </div>
    </x-titlebar>

    {!! $table !!}
@endsection
