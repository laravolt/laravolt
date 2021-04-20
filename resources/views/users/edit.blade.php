<x-laravolt::layout.app :title="__('laravolt::label.edit_user')">

    <x-slot name="actions">
        <x-laravolt::backlink :url="route('epicentrum::users.index')" />
    </x-slot>

    <x-laravolt::panel :title="$user->name">
        <div class="ui tabular secondary pointing menu left attached">
            <a class="item {{ ($tab == 'account')?'active':'' }}"
               href="{{ route('epicentrum::account.edit', $user['id']) }}">@lang('laravolt::menu.account')</a>
            <a class="item {{ ($tab == 'password')?'active':'' }}"
               href="{{ route('epicentrum::password.edit', $user['id']) }}">@lang('laravolt::menu.password')</a>
        </div>
        <div class="ui basic segment bottom attached p-2 b-0" data-tab="first">
            @yield('content-user-edit')
        </div>
    </x-laravolt::panel>

</x-laravolt::layout.app>
