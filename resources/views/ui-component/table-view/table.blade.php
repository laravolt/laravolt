<table class="ui attached table unstackable responsive m-b-0" aria-label="table">
    <thead>
    <tr>
        @foreach($columns as $column)
            @if($column->getSortableColumn())
                <th scope="col"
                    style="cursor: pointer"
                    wire:click.prevent="sortBy('{{ $column->getSortableColumn() }}')"
                        {{ $column->headerAttributes(asHtml:true) }}>

                    @if($column->getSortableColumn() === $sort)
                        <i class="icon caret {{ $direction === 'asc' ? 'up' : 'down' }}"></i>
                    @else
                        <i class="icon sort"></i>
                    @endif
                    {!! $column->getHeader() !!}
                </th>
            @else
                <th scope="col" {!! $column->headerAttributes(asHtml:true) !!}>
                    {!! $column->getHeader() !!}
                </th>
            @endif

        @endforeach
    </tr>
    </thead>
    <tbody class="collection">
    @forelse($data as $item)
        @php($outerLoop = $loop)
        <tr>
            @foreach($columns as $column)
                <td {!! $column->cellAttributes($item) !!}>{!! $column->cell($item, $data, $outerLoop) !!}</td>
            @endforeach
        </tr>
    @empty
        @include('laravolt::ui-component.table-view.empty')
    @endforelse
    </tbody>
</table>
