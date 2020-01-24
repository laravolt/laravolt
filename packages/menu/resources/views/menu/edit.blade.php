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
    @component('laravolt::components.panel', ['title' => __('Edit Menu')])
        {!! form()->bind($menu)->put(route('menu::menu.update', $menu)) !!}
        @include('menu::menu._form')
        {!! form()->close() !!}
    @endcomponent
@endsection

@endsection
