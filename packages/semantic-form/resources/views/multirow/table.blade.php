<table class="ui table">
    <thead>
        <tr>
            @foreach($headers ?? [] as $header)
                <th>{{ $header }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @forelse ($data ?? [] as $row)
            <tr>
                @foreach($row as $cell)
                    <td>{{ $cell }}</td>
                @endforeach
            </tr>
        @empty
            <tr colspan="{{ count($headers ?? []) }}">
                <td>-</td>
            </tr>
        @endforelse
    </tbody>
</table>
