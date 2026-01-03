<x-volt-app :title="__('laravolt::label.permissions')">
    <x-volt-panel :title="__('laravolt::label.manage_permissions')" icon="shield">
        <!-- Tips Alert -->
        <div class="mb-6 flex items-start gap-x-4 rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/50">
            <div class="flex-shrink-0">
                <svg class="size-5 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.478a12.06 12.06 0 01-4.5 0m3.75 2.383a14.406 14.406 0 01-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 10-7.517 0c.85.493 1.509 1.333 1.509 2.316V18" />
                </svg>
            </div>
            <div>
                <h4 class="text-sm font-semibold text-blue-800 dark:text-blue-200">
                    Tips
                </h4>
                <p class="mt-1 text-sm text-blue-700 dark:text-blue-300">
                    @lang('laravolt::message.permission_description_tip')
                    <br>
                    @lang('laravolt::message.permission_description_example')
                </p>
            </div>
        </div>

        {!! form()->open(route('epicentrum::permissions.update'))->put() !!}

        <!-- Permissions Table -->
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200 dark:divide-neutral-700 border border-gray-200 dark:border-neutral-700 rounded-lg">
                <thead class="bg-gray-50 dark:bg-neutral-800">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-neutral-400 w-16">
                            No
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-neutral-400 w-64">
                            @lang('laravolt::permissions.name')
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-neutral-400">
                            @lang('laravolt::permissions.description')
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-neutral-900 dark:divide-neutral-700">
                    @foreach($permissions as $index => $permission)
                        <tr class="hover:bg-gray-50 dark:hover:bg-neutral-800 transition-colors">
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-neutral-400">
                                {{ $index + 1 }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-x-2">
                                    <span class="inline-flex items-center justify-center size-8 rounded-lg bg-gray-100 dark:bg-neutral-700">
                                        <svg class="size-4 text-gray-600 dark:text-neutral-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                                        </svg>
                                    </span>
                                    <code class="text-sm font-mono text-gray-900 dark:text-white">{{ $permission->name }}</code>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <textarea
                                    name="permission[{{ $permission->getKey() }}]"
                                    rows="2"
                                    class="py-2 px-3 block w-full border border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-300 dark:placeholder-neutral-500 dark:focus:ring-neutral-600 resize-none"
                                    placeholder="@lang('laravolt::placeholder.permission_description')"
                                >{{ $permission->description }}</textarea>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center gap-x-3 mt-6">
            <x-volt-button>@lang('laravolt::action.save')</x-volt-button>
        </div>

        {!! form()->close() !!}

    </x-volt-panel>

</x-volt-app>
