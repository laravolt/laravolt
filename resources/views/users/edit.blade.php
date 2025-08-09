<x-volt-app :title="__('laravolt::label.edit_user')">

    <x-slot name="actions">
        <x-volt-backlink :url="route('epicentrum::users.index')" />
    </x-slot>

    <x-volt-panel :title="$user->name">
        <nav class="flex border-b border-gray-200" aria-label="Tabs">
            <a class="-mb-px py-2 px-3 inline-flex items-center gap-x-2 border-b-2 text-sm {{ ($tab == 'account') ? 'border-teal-600 font-semibold text-teal-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}"
               href="{{ route('epicentrum::account.edit', $user['id']) }}">@lang('laravolt::menu.account')</a>
            <a class="-mb-px py-2 px-3 inline-flex items-center gap-x-2 border-b-2 text-sm {{ ($tab == 'password') ? 'border-teal-600 font-semibold text-teal-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}"
               href="{{ route('epicentrum::password.edit', $user['id']) }}">@lang('laravolt::menu.password')</a>
        </nav>
        <div class="p-3">
            @yield('content-user-edit')
        </div>
    </x-volt-panel>

</x-volt-app>
