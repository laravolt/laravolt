@extends(
    config('laravolt.menu-manager.view.layout'),
    [
        '__page' => [
            'title' => __('Menu'),
            'actions' => [
                [
                    'label' => __('Kembali ke Menu'),
                    'class' => '',
                    'icon' => 'icon arrow left',
                    'url' => route('menu-manager::menu.index')
                ],
            ]
        ],
    ]
)

@section('content')

@section('content')
    @component('ui::components.panel', ['title' => __('Tambah Menu')])
        {!! form()->post(route('menu-manager::menu.store')) !!}
        @include('menu-manager::menu._form')
        {!! form()->close() !!}
    @endcomponent
@endsection

@endsection
