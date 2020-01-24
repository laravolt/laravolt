@extends(
    config('laravolt.lookup.view.layout'),
    [
        '__page' => [
            'title' => __('Edit Lookup'),
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
        {!! form()->bind($lookup)->put(route('lookup::lookup.update', $lookup)) !!}
        @include('lookup::lookup._form')
        {!! form()->close() !!}
    @endcomponent
@endsection
