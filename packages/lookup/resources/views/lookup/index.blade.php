@extends(
    config('laravolt.lookup.view.layout'),
    [
        '__page' => [
            'title' => 'Lookup',
            'actions' => [
                [
                    'label' => __('Tambah'),
                    'class' => 'primary',
                    'icon' => 'icon plus circle',
                    'url' => route('lookup::lookup.create', $collection)
                ],
            ]
        ],
    ]
)

@section('content')
    {!! $table !!}
@endsection
