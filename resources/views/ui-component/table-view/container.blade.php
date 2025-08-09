<div data-role="suitable" class="bg-white border border-gray-200 rounded-xl shadow-sm">
    <div class="h-1 bg-gray-100">
        <div class="h-1 bg-teal-600 transition-all" wire:loading.class="w-full" style="width:0"></div>
    </div>

    @if($this->filters() || $this->showSearchbox)
        <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between gap-3" data-role="suitable-header">
            <div class="flex-1">
                @if($this->showSearchbox)
                    <div>
                        @include('laravolt::ui-component.shared.searchbox')
                    </div>
                @endif
            </div>
            <div>
                @include('laravolt::ui-component.table-view.filter')
            </div>
        </div>
    @endif

    @include('laravolt::ui-component.table-view.table')

    <footer class="px-4 py-3 border-t border-gray-200 flex items-center justify-between">
        @if($data instanceof \Illuminate\Contracts\Pagination\Paginator)
            <div>
                <small class="text-sm text-gray-600">{{ $this->summary() }}</small>
            </div>

            @if($showPerPage)
                <div class="relative">
                    <button type="button" class="hs-dropdown-toggle inline-flex items-center gap-x-1.5 rounded-md border border-gray-300 bg-white px-3 py-1.5 text-sm text-gray-700 shadow-sm hover:bg-gray-50">Per page: <span class="font-semibold">{{ request('per_page', $data->perPage()) }}</span>
                        <svg class="h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div class="hs-dropdown-menu mt-1 hidden z-10 w-24 rounded-md border border-gray-200 bg-white p-1 shadow-md">
                        @foreach($perPageOptions as $n)
                            <button type="button" class="w-full text-left px-2 py-1.5 text-sm rounded hover:bg-gray-100" wire:click.prevent="changePerPage({{ $n }})">{{ $n }}</button>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="ml-auto">
                {!! $data->appends(request()->input())->onEachSide(1)->links($paginationView) !!}
            </div>
        @endif
    </footer>

</div>
