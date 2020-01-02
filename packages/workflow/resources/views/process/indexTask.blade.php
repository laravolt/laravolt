@extends(
    config('laravolt.workflow.view.layout'),
    [
        '__page' => [
            'title' => $module->label,
            'actions' => [
            ]
        ],
    ]
)

@section('content')
    {!! $table !!}
@endsection
