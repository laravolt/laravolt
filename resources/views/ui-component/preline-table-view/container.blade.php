<div data-role="suitable" class="p-5 space-y-4 flex flex-col bg-white border border-gray-200 shadow-2xs rounded-xl dark:bg-neutral-800 dark:border-neutral-700">
    @if($this->filters() || $this->showSearchbox)
        <!-- Filter Group -->
        <div class="grid md:grid-cols-2 gap-y-2 md:gap-y-0 md:gap-x-5" data-role="suitable-header">
            <div>
                @if($this->showSearchbox)
                    @include('laravolt::ui-component.shared.searchbox')
                @endif
            </div>
            <!-- End Col -->

            <div class="flex justify-end items-center gap-x-2">
                @include('laravolt::ui-component.table-view.filter')
            </div>
            <!-- End Col -->
        </div>
        <!-- End Filter Group -->
    @endif

    <div>
        <!-- Table Section -->
        <div class="overflow-x-auto [&::-webkit-scrollbar]:h-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-neutral-700 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500">
            <div class="min-w-full inline-block align-middle">
                @include('laravolt::ui-component.table-view.table')
            </div>
        </div>
        <!-- End Table Section -->

        @if($data instanceof \Illuminate\Contracts\Pagination\Paginator)
            <!-- Footer -->
            <div class="grid grid-cols-2 items-center gap-y-2 sm:gap-y-0 sm:gap-x-5 mt-4">
                <div class="flex items-center gap-x-3">
                    <p class="text-sm text-gray-800 dark:text-neutral-200">
                        <span class="font-medium">{{ $this->summary() }}</span>
                    </p>

                    @if($showPerPage)
                        <div class="hs-dropdown relative inline-flex">
                            <button type="button" class="py-1.5 px-3 inline-flex items-center gap-x-1.5 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-2xs hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none focus:outline-hidden focus:bg-gray-50 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700" aria-haspopup="menu" aria-expanded="false" aria-label="Items per page">
                                {{ request('per_page', $data->perPage()) }} per page
                                <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m6 9 6 6 6-6" />
                                </svg>
                            </button>

                            <div class="hs-dropdown-menu hs-dropdown-open:opacity-100 w-32 transition-[opacity,margin] duration opacity-0 hidden z-10 bg-white rounded-xl shadow-xl dark:bg-neutral-900" role="menu">
                                <div class="p-1">
                                    @foreach($perPageOptions as $n)
                                        <button type="button" class="w-full flex items-center gap-x-3 py-1.5 px-2 rounded-lg text-sm text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-neutral-300 focus:outline-hidden focus:bg-gray-100 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800" wire:click.prevent="changePerPage({{ $n }})">
                                            {{ $n }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Pagination -->
                <nav class="flex justify-end items-center gap-x-1" aria-label="Pagination">
                    {!! $data->appends(request()->input())->onEachSide(1)->links($paginationView) !!}
                </nav>
                <!-- End Pagination -->
            </div>
            <!-- End Footer -->
        @endif
    </div>
</div>
