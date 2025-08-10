<form method="GET" action="{{ url()->current() }}">
    <div class="relative">
        @foreach(collect(request()->query())->except('page', $name) as $queryString => $value)
            @if(is_string($value))
            <input type="hidden" name="{{ $queryString }}" value="{{ $value }}">
            @endif
        @endforeach
        <input class="block w-full rounded-lg border-gray-200 pe-10 text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-300" name="{{ $name }}" value="{{ request($name) }}" type="text" placeholder="@lang('suitable::suitable.search_placeholder')">
        <button class="absolute inset-y-0 end-0 m-1 inline-flex items-center justify-center rounded-md border border-gray-200 bg-white px-2 text-gray-700 hover:bg-gray-50 focus:outline-hidden focus:ring-2 focus:ring-gray-200 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-300" aria-label="Search">
            🔍
        </button>
    </div>
</form>
