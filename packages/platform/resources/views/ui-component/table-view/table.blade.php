<?php

$tableClass = '';
if ($showHeader && $showFooter) {
    $tableClass = 'attached';
} elseif ($showHeader) {
    $tableClass = 'bottom attached';
} elseif ($showFooter) {
    $tableClass = 'top attached';
}
?>

<table class="ui {{ $tableClass }} table unstackable responsive" aria-label="table">
    <thead>
    <tr>
        @foreach($columns as $column)
            @if($column->getSortableColumn())
                <th scope="col"
                    style="cursor: pointer"
                    wire:click.prevent="sortBy('{{ $column->getSortableColumn() }}')"
                    {{ array_to_html_attributes($column->headerAttributes()) }}>

                    @if($column->getSortableColumn() === $sort)
                        <i class="icon caret {{ $direction === 'asc' ? 'up' : 'down' }}"></i>
                    @else
                        <i class="icon sort"></i>
                    @endif
                    {!! $column->getHeader() !!}
                </th>
            @else
                <th scope="col" {!! array_to_html_attributes($column->headerAttributes()) !!}>
                    {!! $column->getHeader() !!}
                </th>
            @endif

        @endforeach
    </tr>
    @if($hasSearchableColumns)
        <tr class="ui form" data-role="suitable-header-searchable">
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
        @include('laravolt::ui-component.table-view.empty')
    @endforelse
    </tbody>
</table>
