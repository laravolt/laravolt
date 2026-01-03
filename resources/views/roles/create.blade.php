<x-volt-app :title="__('laravolt::label.roles')">

    <x-slot name="actions">
        <x-volt-backlink url="{{ route('epicentrum::roles.index') }}"></x-volt-backlink>
    </x-slot>

    <x-volt-panel title="{{ __('laravolt::label.add_role') }}" icon="user-astronaut">
        {!! Form::open()->post()->action(route('epicentrum::roles.store')) !!}

        <!-- Role Name Input -->
        <div class="mb-6">
            <label for="name" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                {{ trans('laravolt::roles.name') }} <span class="text-red-500">*</span>
            </label>
            <input type="text"
                   id="name"
                   name="name"
                   value="{{ old('name') }}"
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
                               onclick="toggleAllPermissions(this)">
                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-neutral-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-neutral-600 peer-checked:bg-blue-600"></div>
                        <span class="ms-3 text-sm font-medium text-gray-700 dark:text-neutral-300">
                            @lang('laravolt::action.select_all')
                        </span>
                    </label>
                </div>
            </div>

            <!-- Permissions List -->
            <div class="divide-y divide-gray-200 dark:divide-neutral-700">
                @foreach($permissions as $permission)
                    <label class="flex items-center gap-x-4 px-6 py-4 hover:bg-gray-50 dark:hover:bg-neutral-700/50 cursor-pointer transition-colors">
                        <input type="checkbox"
                               name="permissions[]"
                               value="{{ $permission->id }}"
                               class="permission-checkbox shrink-0 mt-0.5 border-gray-200 rounded text-blue-600 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-neutral-800"
                               {{ old('permissions') && in_array($permission->id, old('permissions')) ? 'checked' : '' }}>
                        <div class="grow">
                            <span class="block text-sm font-medium text-gray-900 dark:text-white">
                                {{ $permission->name }}
                            </span>
                            @if($permission->description)
                                <span class="block text-sm text-gray-500 dark:text-neutral-400">
                                    {{ $permission->description }}
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

    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const allChecked = [...checkboxes].every(c => c.checked);
            const someChecked = [...checkboxes].some(c => c.checked);
            selectAll.checked = allChecked;
            selectAll.indeterminate = someChecked && !allChecked;
        });
    });
});
</script>
