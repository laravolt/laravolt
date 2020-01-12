@extends(config('laravolt.epicentrum.view.layout'))

@section('page.title', __('laravolt::label.edit_user'))

@push('page.actions')
    <a href="{{ route('epicentrum::users.index') }}" class="ui button">
        <i class="icon arrow up"></i> Kembali ke Index
    </a>
@endpush

@section('content')
    <div class="ui segment p-0 secondary">
        <div class="p-1">
            <div class="ui list horizontal">
                <div class="item">
                    <h3 class="ui header">
                        <img class="ui image avatar" src="{{ $user->avatar }}" alt=""> {{ $user->name }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="ui tabular menu left attached">
            <a class="item {{ ($tab == 'account')?'active':'' }}" href="{{ route('epicentrum::account.edit', $user['id']) }}">@lang('laravolt::menu.account')</a>
            <a class="item {{ ($tab == 'password')?'active':'' }}" href="{{ route('epicentrum::password.edit', $user['id']) }}">@lang('laravolt::menu.password')</a>
        </div>
        <div class="ui segment bottom attached p-1 b-0" data-tab="first">
            @yield('content-user-edit')
        </div>
    </div>
@endsection
