@extends(config('laravolt.epicentrum.view.layout'))

@section('content')

    <x-laravolt::titlebar :title="__('laravolt::label.users')">
        <div class="item">
            <x-laravolt::link-button url="{{ route('epicentrum::users.create') }}">
                <i class="icon plus"></i> @lang('laravolt::action.add')
            </x-laravolt::link-button>
        </div>
    </x-laravolt::titlebar>

    {!! $table !!}
@endsection
