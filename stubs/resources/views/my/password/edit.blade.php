<x-volt-app :title="__('Edit Password')">
    <x-volt-panel title="{{ __('Edit Password') }}" icon="user-lock">
        {!! form()->open()->action(route('my::password.update')) !!}
            {!! form()->password('password_current')->label(__('Current Password')) !!}
            {!! form()->password('password')->label(__('New Password')) !!}
            {!! form()->password('password_confirmation')->label(__('Confirm New Password')) !!}

            <div class="mt-4">
                <button type="submit" class="inline-flex items-center justify-center gap-x-2 rounded-lg text-sm font-medium focus:outline-hidden transition-all disabled:opacity-50 disabled:pointer-events-none px-3.5 py-2.5 bg-blue-600 text-white hover:bg-blue-700 focus:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">{{ __('Save') }}</button>
            </div>
        {!! form()->close() !!}
    </x-volt-panel>
</x-volt-app>
