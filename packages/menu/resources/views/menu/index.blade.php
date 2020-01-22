@extends(
    config('laravolt.menu.view.layout'),
    [
        '__page' => [
            'title' => __('Menu'),
            'actions' => [
                [
                    'label' => __('Tambah'),
                    'class' => 'primary',
                    'icon' => 'icon plus circle',
                    'url' => route('menu::menu.create')
                ],
                ['url' => route('menu::menu.download'), 'label' => '', 'icon' => 'download', 'class' => 'icon'],
            ]
        ],
    ]
)

@section('content')
    {!! $table !!}
@endsection
