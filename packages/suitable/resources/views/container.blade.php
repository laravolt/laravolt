<div id="{{ $id }}" data-role="suitable" class="x-suitable">
  @foreach($segments as $segment)
    @unless($segment->isEmpty())
      <div class="flex items-center justify-between px-4 py-2 sm:px-6 lg:px-8">
        {!! $segment->render() !!}
        <div>
          @if($collection instanceof \Illuminate\Contracts\Pagination\Paginator)
            {!! $collection->appends(request()->input())->onEachSide(1)->links('laravolt::pagination.simple') !!}
          @endif
        </div>
      </div>
    @endunless
  @endforeach

  @include('suitable::table')

  @if($showFooter)
    <footer class="flex items-center justify-between px-4 py-2 sm:px-6 lg:px-8">
      <div class="text-xs text-gray-500 dark:text-neutral-400">
        {{ $builder->summary() }}
      </div>

      @if($showPerPage)
        <div class="relative">
          <div class="inline-flex items-center gap-x-2">
            <span class="text-xs text-gray-500 dark:text-neutral-400">Per page:</span>
            <div class="hs-dropdown [--placement:bottom-right] inline-flex">
              <button type="button" class="hs-dropdown-toggle py-1.5 px-2 inline-flex items-center gap-x-1 text-xs rounded-lg border border-gray-200 bg-white text-gray-700 shadow-2xs hover:bg-gray-50 focus:outline-hidden dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-300">
                <span>{{ request('per_page', $collection->perPage()) }}</span>
                <svg class="size-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
              </button>
              <div class="hs-dropdown-menu hidden z-10 mt-2 min-w-28 bg-white border border-gray-200 rounded-lg p-1 shadow-2xs dark:bg-neutral-800 dark:border-neutral-700">
                @foreach($perPageOptions as $n)
                  <a class="block w-full text-left py-1.5 px-2 text-xs text-gray-700 hover:bg-gray-100 rounded-md dark:text-neutral-300 dark:hover:bg-neutral-700" href="{!! request()->fullUrlWithQuery(['per_page' => $n]) !!}">{{ $n }}</a>
                @endforeach
              </div>
            </div>
          </div>
        </div>
      @endif

      @if($collection instanceof \Illuminate\Contracts\Pagination\Paginator)
        {!! $collection->appends(request()->input())->onEachSide(1)->links($paginationView) !!}
      @endif
    </footer>
  @endif
</div>

@if($hasSearchableColumns)
  <form id="suitable-form-searchable" action="{{ request()->url() }}" data-role="suitable-form-searchable" class="hidden">
    <input type="submit">
  </form>
@endif
