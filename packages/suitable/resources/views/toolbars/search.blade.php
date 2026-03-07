<form method="GET" action="{{ url()->current() }}">
    @foreach(collect(request()->query())->except('page', $name) as $queryString => $value)
        @if(is_string($value))
        <input type="hidden" name="{{ $queryString }}" value="{{ $value }}">
        @endif
    @endforeach
    <div class="relative flex items-center">
        <input
            name="{{ $name }}"
            value="{{ request($name) }}"
            type="text"
            placeholder="@lang('suitable::suitable.search_placeholder')"
            class="py-2 px-3 pe-11 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
        >
        <button type="submit" class="absolute inset-y-0 end-0 flex items-center justify-center w-10 text-gray-500 hover:text-gray-700 dark:text-neutral-400 dark:hover:text-neutral-300">
            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
        </button>
    </div>
</form>
