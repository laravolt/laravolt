<form method="GET" action="{{ url()->current() }}">
  <div class="flex items-center gap-2">
    @foreach(collect(request()->query())->except('page', $name) as $queryString => $value)
      @if(is_string($value))
        <input type="hidden" name="{{ $queryString }}" value="{{ $value }}">
      @endif
    @endforeach
    <div class="relative">
      <input class="py-2 px-3 pe-9 block w-64 border border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-300 dark:placeholder-neutral-500 dark:focus:ring-neutral-600" name="{{ $name }}" value="{{ request($name) }}" type="text" placeholder="@lang('suitable::suitable.search_placeholder')">
      <button class="absolute inset-y-0 end-0 flex items-center justify-center px-2 text-gray-400 hover:text-gray-600" aria-label="Search">
        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
      </button>
    </div>
  </div>
  <noscript>
    <button class="hidden">Search</button>
  </noscript>
</form>
