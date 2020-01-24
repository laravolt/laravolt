@extends(
    config('laravolt.menu.view.layout'),
    [
        '__page' => [
            'title' => __('Menu'),
            'actions' => [
                [
                    'label' => __('Kembali ke Menu'),
                    'class' => '',
                    'icon' => 'icon arrow left',
                    'url' => route('menu::menu.index')
                ],
            ]
        ],
    ]
)

@section('content')

@section('content')
    @component('laravolt::components.panel', ['title' => __('Tambah Menu')])
        {!! form()->post(route('menu::menu.store')) !!}
        @include('menu::menu._form')
        {!! form()->close() !!}
    @endcomponent
@endsection

@endsection
