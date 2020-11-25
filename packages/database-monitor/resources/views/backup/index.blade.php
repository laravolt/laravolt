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

@section('content')
    <x-titlebar title="{{ __('Database Backup') }}">
        <x-item>
            {!! form()->open()->route('database-monitor::backup.store') !!}
            <button type="submit" class="ui button primary">
                <i class="icon folder plus"></i> @lang('Backup Now')
            </button>
            {!! form()->close() !!}

            @include('database-monitor::backup._restore-button')

        </x-item>
    </x-titlebar>

    {!! app('laravolt.file-manager')->openDisk($disk)->render() !!}
@endsection
