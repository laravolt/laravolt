<x-volt-table>
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
    @if($hasSearchableColumns)
    <tr data-role="suitable-header-searchable">
        @foreach($columns as $column)
            @if($column->isSearchable())
                {!! $column->searchableHeader()->render() !!}
            @else
                <th></th>
            @endif
        @endforeach
    </tr>
    @endif
    </thead>
    <tbody class="collection divide-y divide-gray-200 dark:divide-neutral-700">
    @forelse($collection as $data)
        @php($outerLoop = $loop)
        @if($row)
            @include($row)
        @else
            <tr class="hover:bg-gray-50 dark:hover:bg-neutral-800">
                @foreach($columns as $column)
                    <td {!! $column->cellAttributes($data) !!}>{!! $column->cell($data, $collection, $outerLoop) !!}</td>
                @endforeach
            </tr>
        @endif
    @empty
        @include('suitable::empty')
    @endforelse
    </tbody>
</x-volt-table>
