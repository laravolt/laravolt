@extends(config('laravolt.lookup.view.layout'))

@section('content')
    <x-titlebar title="Lookup">
        <div class="item">
            <x-link url="{{ route('lookup::lookup.create', $collection) }}">
                <i class="icon plus"></i> {{ __('Tambah') }}
            </x-link>
        </div>
    </x-titlebar>
    {!! $table !!}
@endsection
