<table class="ui {{ empty($segments) ? 'top': '' }} attached table">
    <thead>
    <tr>
        @foreach($columns as $column)
            {!! $column->header() !!}
        @endforeach
    </tr>
    </thead>
    <tbody class="collection">
    @forelse($collection as $data)
        @php($outerLoop = $loop)
        @if($row)
            @include($row)
        @else
            <tr>
                @foreach($columns as $column)
                    <td {!! $column->cellAttributes($data) !!}>{!! $column->cell($data, $collection, $outerLoop) !!}</td>
                @endforeach
            </tr>
        @endif
    @empty
        @include('suitable::empty')
    @endforelse
    </tbody>
</table>
