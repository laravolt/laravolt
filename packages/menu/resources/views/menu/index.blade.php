@extends(
    config('laravolt.menu-manager.view.layout'),
    [
        '__page' => [
            'title' => __('Menu'),
            'actions' => [
                [
                    'label' => __('Tambah'),
                    'class' => 'primary',
                    'icon' => 'icon plus circle',
                    'url' => route('menu-manager::menu.create')
                ],
                ['url' => route('menu-manager::menu.download'), 'label' => '', 'icon' => 'download', 'class' => 'icon'],
            ]
        ],
    ]
)

@section('content')
    {!! $table !!}
@endsection
