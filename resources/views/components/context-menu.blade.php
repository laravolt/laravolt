@php
    $id = $id ?? 'context-menu-' . uniqid();
    $items = $items ?? [];
@endphp

<div id="{{ $id }}" class="hs-dropdown [--trigger:contextmenu] relative inline-flex" {{ $attributes->except(['items']) }}>
    <div class="hs-dropdown-toggle cursor-context-menu">
        {{ $slot }}
    </div>

    <div class="hs-dropdown-menu hs-dropdown-open:opacity-100 w-48 hidden z-10 transition-[opacity,margin] duration bg-white shadow-md rounded-lg p-1 space-y-0.5 dark:bg-neutral-800 dark:border dark:border-neutral-700 dark:divide-neutral-700" role="menu">
        @foreach($items as $item)
            @php
                $label = is_array($item) ? ($item['label'] ?? '') : $item;
                $icon = is_array($item) ? ($item['icon'] ?? null) : null;
                $href = is_array($item) ? ($item['href'] ?? '#') : '#';
                $divider = is_array($item) && ($item['divider'] ?? false);
                $danger = is_array($item) && ($item['danger'] ?? false);
            @endphp
            @if($divider)
                <div class="border-t border-gray-200 dark:border-neutral-700 my-1"></div>
            @else
                <a class="flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm {{ $danger ? 'text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/30' : 'text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300 dark:focus:bg-neutral-700' }}" href="{{ $href }}">
                    {{ $label }}
                </a>
            @endif
        @endforeach
    </div>
</div>
