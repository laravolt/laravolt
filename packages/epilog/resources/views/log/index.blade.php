@extends(
    config('laravolt.epilog.view.layout'),
    [
        '__page' => [
            'title' => __('Application Log'),
            'actions' => []
        ],
])

@section('content')
    <div class="ui search selection dropdown fluid">
        <div class="text">{{ basename($selectedFile) }}</div>
        <i class="dropdown icon"></i>
        <div class="menu">
            @foreach($files as $file)
                <a data-value="{{ $selectedFile }}"
                   href="{{ route('epilog::log.index', ['file' => urlencode($file['path'])]) }}"
                   class="item">{{ $file['basename'] }}</a>
            @endforeach
        </div>
    </div>

    @if($logs)
        <table class="ui table compact selectable" epilog-table>
            <thead>
            <tr>
                <th></th>
                <th>Level</th>
                <th>Log</th>
                <th>Waktu</th>
            </tr>
            </thead>
            @foreach($logs as $log)
                <tr style="cursor: pointer" data-id="{{ $loop->iteration }}">
                    <td class="numbering">
                        <div class="ui label circular mini empty {{ $log['class'] }}"></div>
                    </td>
                    <td>
                        {{ $log['level'] }}
                    </td>
                    <td>
                        {{ \Illuminate\Support\Str::limit($log['message']) }}

                        <div class="ui modal" data-id="{{ $loop->iteration }}">
                            <i class="close icon"></i>
                            <div class="content scrolling">
                                {{ $log['raw'] }}
                            </div>
                        </div>

                    </td>
                    <td>{{ \Carbon\Carbon::instance($log['date'])->setTimezone(auth()->user()->timezone)->format('H:i:s')  }}</td>
                </tr>
            @endforeach
        </table>
    @endif

@endsection

@push('script')
    <script>
      $(function () {
        $('[epilog-table]').on('click', 'tr', function (e) {
          let id = $(e.currentTarget).data('id');
          $('.ui.modal[data-id="' + id + '"]').modal('show');
        });
      });
    </script>
@endpush
