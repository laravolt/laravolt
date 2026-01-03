<x-volt-app :title="__('laravolt::label.roles')">

    <x-slot name="actions">
        <x-volt-backlink url="{{ route('epicentrum::roles.index') }}"></x-volt-backlink>
    </x-slot>

    <!-- Edit Role Panel -->
    <x-volt-panel title="{{ __('laravolt::label.edit_role') }}" icon="user-astronaut">
        {!! Form::open()->put()->action(route('epicentrum::roles.update', $role['id'])) !!}

        <!-- Role Name Input -->
        <div class="mb-6">
            <label for="name" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                {{ trans('laravolt::roles.name') }} <span class="text-red-500">*</span>
            </label>
            <input type="text"
                   id="name"
                   name="name"
                   value="{{ old('name', $role['name']) }}"
                   required
                   class="py-3 px-4 block w-full border border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                   placeholder="{{ trans('laravolt::roles.name') }}">
            @error('name')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Permissions Section -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-neutral-800 dark:border-neutral-700">
            <!-- Header with Select All -->
            <div class="border-b border-gray-200 px-6 py-4 dark:border-neutral-700">
                <div class="flex items-center justify-between">
                    <h4 class="text-base font-semibold text-gray-900 dark:text-white">
                        @lang('laravolt::label.permissions')
                    </h4>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox"
                               id="select-all-permissions"
                               class="sr-only peer"
                               onclick="toggleAllPermissions(this)"
                               {{ count($assignedPermissions) === $permissions->count() ? 'checked' : '' }}>
                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-neutral-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-neutral-600 peer-checked:bg-blue-600"></div>
                        <span class="ms-3 text-sm font-medium text-gray-700 dark:text-neutral-300">
                            @lang('laravolt::action.select_all')
                        </span>
                    </label>
                </div>
            </div>

            <!-- Hidden input to handle empty permissions submission -->
            <input type="hidden" name="permissions[]" value="0">

            <!-- Permissions List -->
            <div class="divide-y divide-gray-200 dark:divide-neutral-700 max-h-96 overflow-y-auto">
                @foreach($permissions as $permission)
                    <label class="flex items-center gap-x-4 px-6 py-4 hover:bg-gray-50 dark:hover:bg-neutral-700/50 cursor-pointer transition-colors">
                        <input type="checkbox"
                               name="permissions[]"
                               value="{{ $permission->id }}"
                               class="permission-checkbox shrink-0 mt-0.5 border-gray-200 rounded text-blue-600 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-neutral-800"
                               {{ in_array($permission->id, $assignedPermissions) ? 'checked' : '' }}>
                        <div class="grow">
                            <span class="block text-sm font-medium text-gray-900 dark:text-white">
                                {{ $permission->name }}
                            </span>
                            @if($permission->description)
                                <span class="block text-sm text-gray-500 dark:text-neutral-400">
                                    {{ $permission->description ?? 'No description' }}
                                </span>
                            @else
                                <span class="block text-sm text-gray-400 dark:text-neutral-500 italic">
                                    No description
                                </span>
                            @endif
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center gap-x-3 mt-6">
            <x-volt-button>@lang('laravolt::action.save')</x-volt-button>
            <x-volt-link-button
                    variant="secondary"
                    url="{{ route('epicentrum::roles.index') }}">@lang('laravolt::action.cancel')
            </x-volt-link-button>
        </div>

        {!! Form::close() !!}
    </x-volt-panel>

    <!-- Delete Role Panel -->
    <div class="mt-6">
        <x-volt-panel title="{{ __('laravolt::label.delete_role') }}" icon="exclamation-triangle" icon-class="text-red-500">
            <div class="space-y-4">
                <div class="flex items-start gap-x-3">
                    <div class="flex-shrink-0">
                        <span class="inline-flex items-center justify-center size-10 rounded-lg bg-red-100 dark:bg-red-900/50">
                            <svg class="size-5 text-red-600 dark:text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-700 dark:text-neutral-300">
                            @lang('laravolt::message.delete_role_intro', ['count' => $role->users->count()])
                        </p>
                        @if($role->users->count() > 0)
                            <p class="mt-2 text-sm text-amber-600 dark:text-amber-400">
                                <strong>@lang('laravolt::message.warning'):</strong>
                                @lang('laravolt::message.users_will_lose_role')
                            </p>
                        @endif
                    </div>
                </div>

                {!! Form::open()->delete()->action(route('epicentrum::roles.destroy', $role['id'])) !!}
                <div class="pt-2">
                    <x-volt-button
                        variant="danger"
                        type="submit"
                        onclick="return confirm('@lang('laravolt::message.role_deletion_confirmation')')">
                        @lang('laravolt::action.delete')
                    </x-volt-button>
                </div>
                {!! Form::close() !!}
            </div>
        </x-volt-panel>
    </div>

</x-volt-app>

<script>
function toggleAllPermissions(checkbox) {
    const checkboxes = document.querySelectorAll('.permission-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = checkbox.checked;
    });
}

// Update "Select All" state based on individual checkboxes
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('select-all-permissions');
    const checkboxes = document.querySelectorAll('.permission-checkbox');

    function updateSelectAllState() {
        const allChecked = [...checkboxes].every(c => c.checked);
        const someChecked = [...checkboxes].some(c => c.checked);
        selectAll.checked = allChecked;
        selectAll.indeterminate = someChecked && !allChecked;
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateSelectAllState);
    });

    // Initial state update
    updateSelectAllState();
});
</script>
