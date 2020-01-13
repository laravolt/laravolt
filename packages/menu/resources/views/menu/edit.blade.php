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
    @component('ui::components.panel', ['title' => __('Edit Menu')])
        {!! form()->bind($menu)->put(route('menu-manager::menu.update', $menu)) !!}
        @include('menu-manager::menu._form')
        {!! form()->close() !!}
    @endcomponent
@endsection

@endsection
