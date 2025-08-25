<form method="GET" action="" class="ui form" >
    <!-- Search Input -->
    <div class="relative">
        <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none z-20 ps-3.5">
            <x-volt-icon name="search" />
        </div>
        <input type="text"
            class="py-1 sm:py-1.5 ps-10 pe-8 block w-full bg-gray-100 border-transparent rounded-lg sm:text-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-transparent dark:text-neutral-400 dark:placeholder:text-neutral-400 dark:focus:bg-neutral-800 dark:focus:ring-neutral-600"
            name="{{ $searchName }}"
            value="{{ request($searchName) }}"
            placeholder="{{ $this->searchPlaceholder ?? __('laravolt::action.search') }}"
            wire:model.debounce.{{ $searchDebounce }}ms="search">
        @if (isset($this->search) && !empty($this->search))
            <div class="absolute inset-y-0 end-0 flex items-center z-20 pe-1">
                <button type="button"
                    class="inline-flex shrink-0 justify-center items-center size-6 rounded-full text-gray-500 hover:text-blue-600 focus:outline-hidden focus:text-blue-600 dark:text-neutral-500 dark:hover:text-blue-500 dark:focus:text-blue-500"
                    aria-label="Clear search" wire:click="$set('search', '')">
                    <span class="sr-only">Clear</span>
                    <x-volt-icon name="times" />
                </button>
            </div>
        @endif
    </div>
    <!-- End Search Input -->
</form>
