<div class="hs-dropdown [--placement:bottom-left] relative inline-flex">
    <button type="button" class="hs-dropdown-toggle inline-flex items-center gap-x-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800 hover:bg-gray-50 focus:outline-hidden focus:ring-2 focus:ring-gray-200 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-700">
        {{ $options[request($name)] ?? $label }}
        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
    </button>
    <div class="hs-dropdown-menu hidden z-10 mt-2 w-48 rounded-xl border border-gray-200 bg-white p-1 shadow-md dark:bg-neutral-900 dark:border-neutral-800">
        @foreach($options as $key => $value)
            <a class="block rounded-md px-3 py-2 text-sm hover:bg-gray-50 dark:hover:bg-neutral-800" href="{{ url()->current() }}?{{ $name }}={{ $key }}">{{ $value }}</a>
        @endforeach
    </div>
</div>
