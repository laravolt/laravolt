@php
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Str;

    $menuConfig = config('laravolt.menu', []);
    $currentRoute = Route::currentRouteName();

    $groupLabel = null;
    $itemLabel = null;
    $itemUrl = null;

    // First, try to match using menu configuration
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

    // If no menu configuration match, build breadcrumb based on URL segments
    $breadcrumbs = [];
    if (!$groupLabel && !$itemLabel) {
        $urlPath = request()->path();
        $segments = array_filter(explode('/', $urlPath));

        if (!empty($segments) && str_contains($urlPath, 'modules/')) {
            $currentPath = '';
            foreach ($segments as $index => $segment) {
                $currentPath .= ($currentPath ? '/' : '') . $segment;

                // Convert segment to readable format
                $label = Str::title(str_replace(['-', '_'], ' ', $segment));

                // Special handling for common patterns
                if ($segment === 'modules') {
                    $label = 'Modules';
                    $currentPath = '#';
                } elseif ($segment === 'dividend') {
                    $label = 'Dividend';
                } elseif ($segment === 'share') {
                    $label = 'Share';
                } elseif ($segment === 'shareholder') {
                    $label = 'Shareholder';
                }

                $breadcrumbs[] = [
                    'label' => $label,
                    'url' => $currentPath,
                    'is_last' => $index === count($segments) - 1
                ];
            }
        }
    }
@endphp

@if($groupLabel && $itemLabel)
    {{-- Menu-based breadcrumb --}}
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
@elseif(!empty($breadcrumbs))
    {{-- URL-based breadcrumb --}}
    <nav aria-label="Breadcrumb" class="inline-flex justify-center items-center">
        <ol class="flex items-center whitespace-nowrap">
            @foreach($breadcrumbs as $breadcrumb)
                <li class="inline-flex items-center">
                    @if($breadcrumb['is_last'])
                        <span class="text-sm font-semibold text-gray-800 truncate dark:text-neutral-200" aria-current="page">{{ __($breadcrumb['label']) }}</span>
                    @else
                        <a href="{{ url($breadcrumb['url']) }}" class="flex items-center text-sm text-gray-500 hover:text-blue-600 focus:outline-none focus:text-blue-600 dark:text-neutral-500 dark:hover:text-blue-500 dark:focus:text-blue-500">
                            {{ __($breadcrumb['label']) }}
                        </a>
                        <svg class="shrink-0 size-5 text-gray-400 dark:text-neutral-600 mx-2" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M6 13L10 3" stroke="currentColor" stroke-linecap="round"></path>
                        </svg>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif