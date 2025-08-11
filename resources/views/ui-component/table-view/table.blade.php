<div class="overflow-x-auto">
  <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700" aria-label="table">
    <thead class="bg-gray-50 dark:bg-neutral-800/50">
      <tr>
        @foreach($columns as $column)
          @if($column->getSortableColumn())
            <th scope="col" {{ $column->headerAttributes(asHtml:true) }} class="px-3 py-2 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-neutral-400">
              <button type="button" class="inline-flex items-center gap-x-1 group" wire:click.prevent="sortBy('{{ $column->getSortableColumn() }}')">
                <span class="group-hover:underline">{!! $column->getHeader() !!}</span>
                @if($column->getSortableColumn() === $sort)
                  @if($direction === 'asc')
                    <svg class="size-3 text-gray-400 group-hover:text-gray-600 dark:text-neutral-500 dark:group-hover:text-neutral-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                  @else
                    <svg class="size-3 text-gray-400 group-hover:text-gray-600 dark:text-neutral-500 dark:group-hover:text-neutral-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                  @endif
                @else
                  <svg class="size-3 text-gray-300 group-hover:text-gray-400 dark:text-neutral-600 dark:group-hover:text-neutral-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 10h10M7 14h7"/></svg>
                @endif
              </button>
            </th>
          @else
            <th scope="col" {!! $column->headerAttributes(asHtml:true) !!} class="px-3 py-2 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-neutral-400">
              {!! $column->getHeader() !!}
            </th>
          @endif
        @endforeach
      </tr>
    </thead>

    <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
      @forelse($data as $item)
        @php($outerLoop = $loop)
        <tr class="bg-white dark:bg-neutral-800">
          @foreach($columns as $column)
            <td {!! $column->cellAttributes($item) !!} class="px-3 py-2 text-sm text-gray-700 dark:text-neutral-300">{!! $column->cell($item, $data, $outerLoop) !!}</td>
          @endforeach
        </tr>
      @empty
        @include('laravolt::ui-component.table-view.empty')
      @endforelse
    </tbody>
  </table>
</div>
