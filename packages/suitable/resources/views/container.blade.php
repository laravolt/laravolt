<div id="{{ $id }}" data-role="suitable" class="space-y-3 x-suitable">

    @foreach($segments as $segment)
        @unless($segment->isEmpty())
            <div class="flex items-center justify-between rounded-2xl border border-gray-200 bg-white p-3 dark:bg-neutral-800 dark:border-neutral-700">
                <div class="flex flex-wrap gap-2">
                    {!! $segment->render() !!}
                </div>
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
        <footer class="flex items-center justify-between rounded-2xl border border-gray-200 bg-white px-4 py-3 dark:bg-neutral-800 dark:border-neutral-700">
            <div class="text-sm text-gray-600 dark:text-neutral-400">
                <small>{{ $builder->summary() }}</small>
            </div>

            @if($showPerPage)
                <div>
                    <label class="sr-only">Per page</label>
                    <select class="block w-24 rounded-lg border-gray-200 text-gray-800 text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-300" onchange="window.location=this.value">
                        @foreach($perPageOptions as $n)
                            <option value="{!! request()->fullUrlWithQuery(['per_page' => $n]) !!}" @selected(request('per_page', $collection->perPage())==$n)>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if($collection instanceof \Illuminate\Contracts\Pagination\Paginator)
                {!! $collection->appends(request()->input())->onEachSide(1)->links($paginationView) !!}
            @endif
        </footer>
    @endif
</div>

@if($hasSearchableColumns)
    <form id="suitable-form-searchable" action="{{ request()->url() }}" data-role="suitable-form-searchable"
          style="display: none">
        <input type="submit">
    </form>
@endif
