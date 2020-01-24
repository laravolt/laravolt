@extends(
    'laravolt::layouts.app',
    [
        '__page' => [
            'title' => 'Form Fields',
            'actions' => [
                ['url' => route('segment.create'), 'label' => 'Tambah Segment', 'icon' => 'plus', 'class' => 'primary'],
                ['url' => route('managementcamunda.create'), 'label' => 'Tambah Field', 'icon' => 'plus', 'class' => 'primary'],
                ['url' => route('managementcamunda.download'), 'label' => '', 'icon' => 'download', 'class' => 'icon'],
            ]
        ],
    ]
)

@section('content')
    {!! $table !!}
@endsection
