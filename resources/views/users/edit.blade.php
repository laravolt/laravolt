<x-volt-app :title="__('laravolt::label.edit_user')">

    <x-slot name="actions">
        <x-volt-backlink :url="route('epicentrum::users.index')" />
    </x-slot>

    <x-volt-panel :title="$user->name">
        <!-- Tab Navigation -->
        <nav class="flex gap-x-1 border-b border-gray-200 dark:border-neutral-700 mb-4" aria-label="Tabs" role="tablist">
            <a class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-medium text-center border-b-2 rounded-t-lg {{ ($tab == 'account') ? 'border-blue-600 text-blue-600 dark:border-blue-500 dark:text-blue-500' : 'border-transparent text-gray-500 hover:text-blue-600 hover:border-blue-300 dark:text-neutral-400 dark:hover:text-blue-500' }}"
               href="{{ route('epicentrum::account.edit', $user['id']) }}">@lang('laravolt::menu.account')</a>
            <a class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-medium text-center border-b-2 rounded-t-lg {{ ($tab == 'password') ? 'border-blue-600 text-blue-600 dark:border-blue-500 dark:text-blue-500' : 'border-transparent text-gray-500 hover:text-blue-600 hover:border-blue-300 dark:text-neutral-400 dark:hover:text-blue-500' }}"
               href="{{ route('epicentrum::password.edit', $user['id']) }}">@lang('laravolt::menu.password')</a>
        </nav>
        <!-- Tab Content -->
        <div class="pt-2">
            @yield('content-user-edit')
        </div>
    </x-volt-panel>

</x-volt-app>
