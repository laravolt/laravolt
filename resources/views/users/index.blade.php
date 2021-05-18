<x-volt-app :title="__('laravolt::label.users')">

    <x-slot name="actions">
        <x-volt-link-button
                :url="route('epicentrum::users.create')"
                icon="plus"
                :label="__('laravolt::action.add')"/>
    </x-slot>

    @livewire('laravolt::user-table')

</x-volt-app>
