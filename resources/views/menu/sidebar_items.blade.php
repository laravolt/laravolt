@php
    use Illuminate\Support\Str;

    // Recursive helper to determine if an item or any descendant is active
    $isExpandedRecursive = function ($menuItem) use (&$isExpandedRecursive) {
        if ($menuItem->isActive) {
            return true;
        }
        foreach ($menuItem->children() as $child) {
            if ($isExpandedRecursive($child)) {
                return true;
            }
        }
        return false;
    };

    /**
     * Render a list of menu items recursively as Preline accordions.
     *
     * @param \Illuminate\Support\Collection|array $list
     * @param int $level
     * @param string $pathKey unique path accumulator for IDs
     */
    $renderItems = function ($list, int $level = 0, string $pathKey = 'root') use (
        &$renderItems,
        $isExpandedRecursive,
    ) {
        $sorted = collect($list)->sortBy(fn($i) => $i->data('order'));
        foreach ($sorted as $loopIndex => $item) {
            $hasChildren = $item->hasChildren();
            $expanded = $hasChildren && $isExpandedRecursive($item);
            $baseId = 'sb-' . $pathKey . '-' . $loopIndex . '-' . Str::slug($item->title);
            $subId = $baseId . '-sub';
            $indentPadding = $level === 0 ? 'px-2 lg:px-5' : 'ps-5 pe-2';
            if ($hasChildren) {
                echo '<li class="hs-accordion ' .
                    $indentPadding .
                    ' ' .
                    ($expanded ? 'active' : '') .
                    '" id="' .
                    e($baseId) .
                    '">';
                echo '<button type="button" class="hs-accordion-toggle hs-accordion-active:bg-gray-100 w-full text-start flex gap-x-3 py-2 px-3 text-sm text-gray-800 rounded-lg hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:hs-accordion-active:bg-neutral-700 focus:outline-hidden focus:bg-gray-100 dark:hover:bg-neutral-700 dark:text-neutral-300 dark:focus:bg-neutral-700" aria-expanded="' .
                    ($expanded ? 'true' : 'false') .
                    '" aria-controls="' .
                    e($subId) .
                    '">';
                // Icon (if any)
                if ($icon = $item->data('icon')) {
                    echo svg(config('laravolt.ui.iconset') . '-' . $icon, null, [
                        'class' => 'shrink-0 mt-0.5 size-4',
                    ])->toHtml();
                }
                echo '<span>' . e($item->title) . '</span>';
                echo '<svg class="hs-accordion-active:-rotate-180 shrink-0 mt-1 size-3.5 ms-auto transition" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6" /></svg>';
                echo '</button>';

                echo '<div id="' .
                    e($subId) .
                    '" class="hs-accordion-content w-full overflow-hidden transition-[height] duration-300" role="region" aria-labelledby="' .
                    e($baseId) .
                    '" style="' .
                    ($expanded ? '' : 'display: none') .
                    '">';
                // Wrapper UL for children
                $groupClasses = 'mt-1 flex flex-col gap-y-1';
                if ($level === 0) {
                    $groupClasses .=
                        ' hs-accordion-group ps-7 relative before:absolute before:top-0 before:start-4.5 before:w-0.5 before:h-full before:bg-gray-100 dark:before:bg-neutral-700';
                } else {
                    $groupClasses .= ' hs-accordion-group ps-4';
                }
                echo '<ul class="' . $groupClasses . '" data-hs-accordion-always-open>'; // children list
                $renderItems($item->children(), $level + 1, $pathKey . '-' . $loopIndex);
                echo '</ul>';
                echo '</div>';
                echo '</li>';
            } else {
                $activeClasses = $item->isActive ? ' bg-gray-100 dark:bg-neutral-700' : '';
                echo '<li class="' . $indentPadding . '">';
                echo '<a href="' .
                    e($item->url()) .
                    '" class="flex gap-x-3 py-2 px-3 text-sm text-gray-800 rounded-lg hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 dark:hover:bg-neutral-700 dark:text-neutral-300 dark:focus:bg-neutral-700' .
                    $activeClasses .
                    '">';
                if ($icon = $item->data('icon')) {
                    echo svg(config('laravolt.ui.iconset') . '-' . $icon, null, [
                        'class' => 'shrink-0 mt-0.5 size-4',
                    ])->toHtml();
                }
                echo '<span>' . e($item->title) . '</span>';
                echo '</a>';
                echo '</li>';
            }
        }
    };
@endphp

{!! $renderItems($items, 0, 'root') !!}
