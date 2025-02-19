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
                @foreach($schema as $key => $field)
                    <td>{{ $row[$key] ?? '' }}</td>
                @endforeach
            </tr>
        @empty
            <tr colspan="{{ count($headers ?? []) }}">
                <td>-</td>
            </tr>
        @endforelse
    </tbody>
</table>
