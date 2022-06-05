<x-volt-app :title="__('Edit Password')">
    <x-volt-panel title="{{ __('Edit Password') }}" icon="user-lock">
        {!! form()->open()->action(route('my::password.update'))->horizontal() !!}
        {!! form()->password('password_current')->label(__('Current Password')) !!}
        {!! form()->password('password')->label(__('New Password')) !!}
        {!! form()->password('password_confirmation')->label(__('Confirm New Password')) !!}
        {!! form()->action(form()->submit(__('Save'))) !!}
        {!! form()->close() !!}
    </x-volt-panel>
</x-volt-app>
