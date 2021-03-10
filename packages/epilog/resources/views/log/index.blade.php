<x-laravolt::layout.app :title="__('Application Log')">
    <x-slot name="actions">
        <div class="ui search selection dropdown">
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

    </x-slot>
    @if($logs)
        <table class="ui table compact selectable" epilog-table>
            <thead>
            <tr>
                <th>Level</th>
                <th>Message</th>
                <th>Time</th>
            </tr>
            </thead>
            @foreach($logs as $log)
                <tr style="cursor: pointer" data-id="{{ $loop->iteration }}">
                    <td class="numbering">
                        <div class="ui label mini {{ $log['class'] }}">{{ $log['level'] }}</div>
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
</x-laravolt::layout.app>
