<div id="{{ $id }}" data-role="suitable" class="x-suitable flex flex-col">
    @foreach($segments as $segment)
        @unless($segment->isEmpty())
            <div class="flex flex-wrap items-center justify-between gap-2 px-4 py-3 border border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 {{ $loop->first ? 'rounded-t-xl' : '' }}">
                <div class="flex flex-wrap items-center gap-2">
                    {!! $segment->render() !!}
                </div>
                <div class="flex items-center gap-2">
                    @if($collection instanceof \Illuminate\Contracts\Pagination\Paginator)
                        {!! $collection->appends(request()->input())->onEachSide(1)->links('laravolt::pagination.simple') !!}
                    @endif
                </div>
            </div>
        @endunless
    @endforeach

    @include('suitable::table')

    @if($showFooter)
        <footer class="flex flex-wrap items-center justify-between gap-2 px-4 py-3 border border-t-0 border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 rounded-b-xl">
            <div class="text-sm text-gray-600 dark:text-neutral-400">
                <small>{{ $builder->summary() }}</small>
            </div>
            @if($showPerPage)
            <div class="hs-dropdown relative inline-flex [--placement:top-left]">
                <button id="hs-suitable-perpage" type="button" class="hs-dropdown-toggle py-1.5 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 focus:outline-none focus:bg-gray-50 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700">
                    {{ request('per_page', $collection->perPage()) }}
                    <svg class="hs-dropdown-open:rotate-180 size-4 transition" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                </button>
                <div class="hs-dropdown-menu hs-dropdown-open:opacity-100 w-20 hidden z-10 transition-[opacity,margin] duration opacity-0 mb-2 bg-white shadow-md rounded-lg dark:bg-neutral-800 dark:border dark:border-neutral-700" role="menu">
                    <div class="p-1 space-y-0.5">
                        @foreach($perPageOptions as $n)
                            <a href="{!! request()->fullUrlWithQuery(['per_page' => $n]) !!}" class="flex items-center gap-x-2 py-1.5 px-3 rounded-lg text-sm text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700 {{ request('per_page', $collection->perPage()) == $n ? 'bg-gray-100 dark:bg-neutral-700' : '' }}">{{ $n }}</a>
                        @endforeach
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
    <form id="suitable-form-searchable" action="{{ request()->url() }}" data-role="suitable-form-searchable" style="display: none">
        <input type="submit">
    </form>
@endif
