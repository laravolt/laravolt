@php
    use Illuminate\Support\Facades\Route;

    $menuConfig = config('laravolt.menu', []);
    $currentRoute = Route::currentRouteName();

    $groupLabel = null;
    $itemLabel = null;
    $itemUrl = null;

    foreach ($menuConfig as $group) {
        foreach ($group as $gLabel => $gData) {
            $items = $gData['menu'] ?? [];
            foreach ($items as $iLabel => $item) {
                $isMatch = false;
                if (isset($item['route']) && $currentRoute === $item['route']) {
                    $isMatch = true;
                } elseif (isset($item['route']) && request()->routeIs($item['route'])) {
                    $isMatch = true;
                } elseif (isset($item['active']) && request()->is($item['active'])) {
                    $isMatch = true;
                }
                if ($isMatch) {
                    $groupLabel = $gLabel;
                    $itemLabel = $iLabel;
                    if (isset($item['route']) && Route::has($item['route'])) {
                        $itemUrl = route($item['route']);
                    }
                    break 2; // stop both loops
                }
            }
        }
    }
@endphp

@if($groupLabel && $itemLabel)
    <nav aria-label="Breadcrumb" class="inline-flex justify-center items-center">
        <ol class="flex items-center whitespace-nowrap">
            <li class="inline-flex items-center">
                <span class="flex items-center text-sm text-gray-500 dark:text-neutral-500">{{ __($groupLabel) }}</span>
                @if($itemLabel !== $groupLabel)
                    <svg class="shrink-0 size-5 text-gray-400 dark:text-neutral-600 mx-2" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M6 13L10 3" stroke="currentColor" stroke-linecap="round"></path>
                    </svg>
                @endif
            </li>
            @if($itemLabel !== $groupLabel)
                <li class="inline-flex items-center text-sm font-semibold text-gray-800 truncate dark:text-neutral-200" aria-current="page">{{ __($itemLabel) }}</li>
            @endif
        </ol>
    </nav>
@endif
