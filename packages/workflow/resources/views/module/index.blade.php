@extends(
    config('laravolt.workflow.view.layout'),
    [
        '__page' => [
            'title' => 'Cockpit',
        ],
    ]
)

@section('content')
    {!! $table !!}
@endsection
