<div class="overflow-x-auto">
  <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700" aria-label="table">
    <thead class="bg-gray-50 dark:bg-neutral-800/50">
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

    <tbody class="divide-y divide-gray-200 dark:divide-neutral-700 collection">
      @forelse($collection as $data)
        @php($outerLoop = $loop)
        @if($row)
          @include($row)
        @else
          <tr class="bg-white dark:bg-neutral-800">
            @foreach($columns as $column)
              <td {!! $column->cellAttributes($data) !!} class="px-3 py-2 text-sm text-gray-700 dark:text-neutral-300">{!! $column->cell($data, $collection, $outerLoop) !!}</td>
            @endforeach
          </tr>
        @endif
      @empty
        @include('suitable::empty')
      @endforelse
    </tbody>
  </table>
</div>
