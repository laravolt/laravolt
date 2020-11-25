@extends(
    config('laravolt.database-monitor.view.layout'),
    [
        '__page' => [
            'title' => 'Backup & Restore',
            'actions' => [
            ]
        ],
    ]
)

@push('page.actions')
    {!! form()->open()->route('database-monitor::backup.store') !!}
    <button type="submit" class="ui button primary">
        <i class="icon folder plus"></i> Backup Now
    </button>
    {!! form()->close() !!}

    @include('database-monitor::backup._restore-button')
@endpush

@section('content')
    {!! app('laravolt.file-manager')->openDisk('local-backup-folder')->render() !!}
@endsection
