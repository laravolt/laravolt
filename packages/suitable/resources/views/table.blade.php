<table class="ui {{ empty($segments) ? 'top': '' }} attached table">
    <thead>
    <tr>
        @foreach($columns as $column)
            @if($column->header() instanceof \Laravolt\Suitable\Contracts\Header)
                {!! $column->header()->render() !!}
            @else
                {!! $column->header() !!}
            @endif
        @endforeach
    </tr>
    @if($columns->first->isSearchable() !== null)
    <tr class="ui form">
        @foreach($columns as $column)
            @if($column->isSearchable())
                <th><input type="text"></th>
            @else
                <th></th>
            @endif
        @endforeach
    </tr>
    @endif
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
