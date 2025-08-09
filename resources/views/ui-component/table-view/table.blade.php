<div class="overflow-x-auto">
<table class="min-w-full divide-y divide-gray-200" aria-label="table">
    <thead class="bg-gray-50">
    <tr>
        @foreach($columns as $column)
            @if($column->getSortableColumn())
                <th scope="col"
                    class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                    wire:click.prevent="sortBy('{{ $column->getSortableColumn() }}')"
                        {{ $column->headerAttributes(asHtml:true) }}>

                    @if($column->getSortableColumn() === $sort)
                        <span class="inline-flex items-center gap-x-1">
                            <span>{!! $column->getHeader() !!}</span>
                            <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/></svg>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-x-1">
                            <span>{!! $column->getHeader() !!}</span>
                            <svg class="h-4 w-4 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11h10M7 15h10M7 7h10"/></svg>
                        </span>
                    @endif
                </th>
            @else
                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" {!! $column->headerAttributes(asHtml:true) !!}>
                    {!! $column->getHeader() !!}
                </th>
            @endif

        @endforeach
    </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
    @forelse($data as $item)
        @php($outerLoop = $loop)
        <tr>
            @foreach($columns as $column)
                <td class="px-3 py-2 text-sm text-gray-700" {!! $column->cellAttributes($item) !!}>{!! $column->cell($item, $data, $outerLoop) !!}</td>
            @endforeach
        </tr>
    @empty
        @include('laravolt::ui-component.table-view.empty')
    @endforelse
    </tbody>
</table>
</div>
