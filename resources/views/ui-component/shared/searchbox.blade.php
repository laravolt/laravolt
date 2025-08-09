<form method="GET" action="">
    <label class="sr-only" for="suitable-search">{{ $this->searchPlaceholder ?? __('laravolt::action.search') }}</label>
    <div class="relative">
        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M10 18a8 8 0 100-16 8 8 0 000 16z"/></svg>
        </div>
        <input
            id="suitable-search"
            type="text"
            name="{{ $searchName }}"
            value="{{ request($searchName) }}"
            placeholder="{{ $this->searchPlaceholder ?? __('laravolt::action.search') }}"
            wire:model.debounce.{{ $searchDebounce }}ms="search"
            class="block w-full rounded-md border-gray-300 pl-10 pr-3 py-2 text-sm placeholder-gray-400 focus:border-teal-500 focus:ring-teal-500"
        >
    </div>
</form>
