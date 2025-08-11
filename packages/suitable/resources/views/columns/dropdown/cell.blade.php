@php
  $placement = $direction === 'left' ? 'bottom-left' : 'bottom-right';
@endphp
<div class="hs-dropdown [--placement:{{ $placement }}] inline-flex">
  <button type="button" class="hs-dropdown-toggle py-1.5 px-2 inline-flex items-center gap-x-1 text-xs rounded-lg border border-gray-200 bg-white text-gray-700 shadow-2xs hover:bg-gray-50 focus:outline-hidden dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-300">
    {!! $text !!}
    <svg class="size-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
  </button>
  <div class="hs-dropdown-menu hidden z-10 mt-2 min-w-36 bg-white border border-gray-200 rounded-lg p-1 shadow-2xs dark:bg-neutral-800 dark:border-neutral-700">
    @foreach($menus as $menu)
      <a class="block w-full text-left py-1.5 px-2 text-xs text-gray-700 hover:bg-gray-100 rounded-md dark:text-neutral-300 dark:hover:bg-neutral-700" href="{{ $menu->url() }}">{!! $menu->title !!}</a>
      @if($menu->divider)
        <div class="my-1 h-px bg-gray-100 dark:bg-neutral-700"></div>
      @endif
    @endforeach
  </div>
</div>
