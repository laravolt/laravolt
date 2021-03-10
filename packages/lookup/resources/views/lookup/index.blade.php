<x-laravolt::layout.app :title="$title">
    <x-slot name="actions">
        <x-laravolt::link-button :label="__('Tambah')" url="{{ route('lookup::lookup.create', $collection) }}" icon="plus">
        </x-laravolt::link-button>
    </x-slot>

    {!! $table !!}

</x-laravolt::layout.app>
