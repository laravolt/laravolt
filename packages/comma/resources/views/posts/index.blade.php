@extends(
    config('laravolt.comma.view.layout'),
    [
        '__page' => [
            'title' => __('Posts'),
            'actions' => [
                [
                    'label' => __('Tambah'),
                    'class' => 'primary',
                    'icon' => 'icon plus circle',
                    'url' => route('comma::posts.create', [$collection])
                ],
            ]
        ],
    ]
)

@section('content')
    {!! Suitable::source($posts)
    ->title(__('comma::post.header.table'))
    ->search(true)
    ->columns([
        \Laravolt\Suitable\Columns\Numbering::make('No'),
        \Laravolt\Suitable\Columns\Text::make('title', __('comma::post.attributes.title'))->sortable(),
        \Laravolt\Suitable\Columns\Text::make('author.name', __('comma::post.attributes.author'))->sortable(),
        \Laravolt\Suitable\Columns\Date::make('created_at', __('comma::post.attributes.date'))->sortable(),
        with(new \Laravolt\Suitable\Columns\RestfulButton('comma::posts'))->only(['edit', 'delete'])->routeParameters(compact('collection'))
    ])
    ->render() !!}

@endsection
