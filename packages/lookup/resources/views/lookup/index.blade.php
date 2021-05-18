<x-volt-app :title="$title">
    <x-slot name="actions">
        <x-volt-link-button :label="__('Tambah')" url="{{ route('lookup::lookup.create', $collection) }}" icon="plus">
        </x-volt-link-button>
    </x-slot>

    {!! $table !!}

</x-volt-app>
