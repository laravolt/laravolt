<x-volt-app :title="__('laravolt::label.roles')">

    <x-slot name="actions">
        <x-volt-link-button
                :url="route('epicentrum::roles.create')"
                icon="plus"
                :label="__('laravolt::action.add')"/>
    </x-slot>

    <!-- Roles Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($roles as $role)
            <a href="{{ route('epicentrum::roles.edit', $role['id']) }}"
               class="group bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-lg hover:border-blue-200 transition-all duration-300 dark:bg-neutral-800 dark:border-neutral-700 dark:hover:border-blue-500">
                <!-- Card Header -->
                <div class="p-5">
                    <div class="flex items-center gap-x-3">
                        <div class="flex-shrink-0">
                            <span class="inline-flex items-center justify-center size-10 rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-400">
                                <svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                </svg>
                            </span>
                        </div>
                        <div class="grow">
                            <h3 class="text-lg font-semibold text-gray-800 group-hover:text-blue-600 transition-colors dark:text-neutral-200 dark:group-hover:text-blue-400">
                                {{ $role['name'] }}
                            </h3>
                        </div>
                    </div>
                </div>

                <!-- Card Footer -->
                <div class="border-t border-gray-200 px-5 py-3 dark:border-neutral-700">
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-x-1.5 text-gray-500 dark:text-neutral-400">
                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                            </svg>
                            <span>{{ $role->users_count }} @lang('laravolt::label.users')</span>
                        </div>
                        <div class="flex items-center gap-x-1.5 text-gray-500 dark:text-neutral-400">
                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" />
                            </svg>
                            <span>{{ $role->permissions_count }} @lang('laravolt::label.permissions')</span>
                        </div>
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    @if($roles->isEmpty())
        <!-- Empty State -->
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <div class="flex-shrink-0 mb-4">
                <span class="inline-flex items-center justify-center size-16 rounded-full bg-gray-100 dark:bg-neutral-800">
                    <svg class="size-8 text-gray-400 dark:text-neutral-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    </svg>
                </span>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-neutral-200">@lang('laravolt::message.no_roles')</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">@lang('laravolt::message.create_first_role')</p>
            <div class="mt-5">
                <x-volt-link-button
                    :url="route('epicentrum::roles.create')"
                    icon="plus"
                    :label="__('laravolt::action.add')"/>
            </div>
        </div>
    @endif

</x-volt-app>
