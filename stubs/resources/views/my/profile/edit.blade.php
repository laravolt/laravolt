<x-volt-app :title="__('Edit Profile')">
    <x-volt-panel title="{{ __('Edit Profile') }}" icon="user-edit">
        {!! form()->bind($user)->put(route('my::profile.update'))->horizontal() !!}

        {!! form()->text('name')->label('Name') !!}
        {!! form()->text('email')->label('Email')->readonly() !!}
        {!! form()->dropdown('timezone', $timezones)->label('Timezone') !!}

        {!! form()->action(form()->submit('Save')) !!}
        {!! form()->close() !!}
    </x-volt-panel>
</x-volt-app>
