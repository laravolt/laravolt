@extends(config('laravolt.epilog.view.layout'))

@section('content')

    <h2 class="ui header">Application Log</h2>

    <div class="ui divider hidden"></div>

    <div class="ui search selection dropdown fluid">
        <div class="text">{{ basename($selectedFile) }}</div>
        <i class="dropdown icon"></i>
        <div class="menu">
            @foreach($files as $file)
                <a data-value="{{ $selectedFile }}" href="{{ route('epilog::log.index', ['file' => urlencode($file['path'])]) }}" class="item">{{ $file['basename'] }}</a>
            @endforeach
        </div>
    </div>

    @if($logs)
        <table class="ui table very compact">
            <thead>
            <tr>
                <th>Waktu</th>
                <th>Logger</th>
                <th>Level</th>
                <th>Pesan</th>
                <th>Context</th>
                <th>Extra</th>
            </tr>
            </thead>
            @foreach($logs as $log)
                <tr>
                    <td>{{ $log['date']->format('H:i:s') }}</td>
                    <td>{{ $log['logger'] }}</td>
                    <td>{{ $log['level'] }}</td>
                    <td>{{ str_limit($log['message']) }}</td>
                    <td>{{ json_encode($log['context']) }}</td>
                    <td>{{ json_encode($log['extra']) }}</td>
                </tr>
            @endforeach
        </table>
    @endif

@endsection
