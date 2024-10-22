<x-volt-app :title="__('laravolt::label.users')">

    <x-slot name="actions">
        <x-volt-link-button
                :url="route('epicentrum::users.create')"
                icon="plus"
                :label="__('laravolt::action.add')"/>
    </x-slot>

    @livewire('volt-user-table', ['headerTitle' => 'Pengguna Terdaftar'])
</x-volt-app>
