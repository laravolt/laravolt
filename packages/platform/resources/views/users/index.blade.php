<x-laravolt::layout.app :title="__('laravolt::label.users')">

    <x-slot name="actions">
        <x-laravolt::link-button
                :url="route('epicentrum::users.create')"
                icon="plus"
                :label="__('laravolt::action.add')"/>
    </x-slot>

    {!! $table !!}

</x-laravolt::layout.app>
