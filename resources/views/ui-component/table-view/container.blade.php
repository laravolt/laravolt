<div data-role="suitable"
    class="p-5 space-y-4 flex flex-col bg-white border border-gray-200 shadow-2xs rounded-xl dark:bg-neutral-800 dark:border-neutral-700">
    @if ($this->filters() || $this->showSearchbox)
        <!-- Filter Group -->
        <div class="grid md:grid-cols-2 gap-y-2 md:gap-y-0 md:gap-x-5" data-role="suitable-header">
            <div>
                @if ($this->showSearchbox)
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
        <div
            class="overflow-x-auto [&::-webkit-scrollbar]:h-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-neutral-700 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500">
            <div class="min-w-full inline-block align-middle">
                @include('laravolt::ui-component.table-view.table')
            </div>
        </div>
        <!-- End Table Section -->

        @if ($data instanceof \Illuminate\Contracts\Pagination\Paginator)
            <!-- Footer -->
            <div class="grid grid-cols-2 items-center gap-y-2 sm:gap-y-0 sm:gap-x-5 mt-4">
                <div class="flex items-center gap-x-3">
                    <p class="text-sm text-gray-800 dark:text-neutral-200">
                        <span class="font-medium">{{ $this->summary() }}</span>
                    </p>

                    @if ($showPerPage)
                        <select
                            class="pe-6 block border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                            wire:model="perPage" wire:change="changePerPage($event.target.value)">
                            @foreach ($perPageOptions as $n)
                                <option value="{{ $n }}"
                                    {{ $n === request('per_page', $data->perPage()) ? 'selected' : '' }}>
                                    {{ $n }}
                                </option>
                            @endforeach
                        </select>
                        {{-- <!-- Floating Select -->
                        <div class="relative w-32">
                            <select
                                class="peer p-4 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:focus:ring-neutral-600 focus:pt-6 focus:pb-2 not-placeholder-shown:pt-6 not-placeholder-shown:pb-2 autofill:pt-6 autofill:pb-2"
                                wire:model="perPage" wire:change="changePerPage($event.target.value)">
                                @foreach ($perPageOptions as $n)
                                    <option value="{{ $n }}"
                                        {{ $n === request('per_page', $data->perPage()) ? 'selected' : '' }}>
                                        {{ $n }}
                                    </option>
                                @endforeach
                            </select>
                            <label
                                class="absolute top-0 start-0 p-4 h-full truncate pointer-events-none transition ease-in-out duration-100 border border-transparent dark:text-white peer-disabled:opacity-50 peer-disabled:pointer-events-none peer-focus:text-xs peer-focus:-translate-y-1.5 peer-focus:text-gray-500 dark:peer-focus:text-neutral-500 peer-not-placeholder-shown:text-xs peer-not-placeholder-shown:-translate-y-1.5 peer-not-placeholder-shown:text-gray-500 dark:peer-not-placeholder-shown:text-neutral-500 dark:text-neutral-500">
                                Rows per page
                            </label>
                        </div>
                        <!-- End Floating Select --> --}}
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
