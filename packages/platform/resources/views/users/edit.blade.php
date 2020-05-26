@extends(config('laravolt.epicentrum.view.layout'))

@section('content')
    <x-titlebar :title="__('laravolt::label.edit_user')">
        <x-backlink url="{{ route('epicentrum::users.index') }}"></x-backlink>
    </x-titlebar>

    <x-panel :title="$user->name">
        <div class="ui tabular secondary pointing menu left attached">
            <a class="item {{ ($tab == 'account')?'active':'' }}" href="{{ route('epicentrum::account.edit', $user['id']) }}">@lang('laravolt::menu.account')</a>
            <a class="item {{ ($tab == 'password')?'active':'' }}" href="{{ route('epicentrum::password.edit', $user['id']) }}">@lang('laravolt::menu.password')</a>
        </div>
        <div class="ui basic segment bottom attached p-2 b-0" data-tab="first">
            @yield('content-user-edit')
        </div>
    </x-panel>
@endsection
