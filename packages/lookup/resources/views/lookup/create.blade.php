@extends(
    config('laravolt.lookup.view.layout'),
    [
        '__page' => [
            'title' => __('Tambah Lookup'),
            'actions' => [
                [
                    'label' => __('Kembali ke Index'),
                    'class' => '',
                    'icon' => 'icon arrow left',
                    'url' => route('lookup::lookup.index', $collection)
                ],
            ]
        ],
    ]
)

@section('content')
    @component('laravolt::components.panel', ['title' => $config['label'] ?? $collection])
        {!! form()->post(route('lookup::lookup.store', $collection)) !!}
        @include('lookup::lookup._form')
        {!! form()->close() !!}
    @endcomponent
@endsection
