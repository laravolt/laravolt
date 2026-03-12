@php
    $id = $attributes->get('id', 'tree-view-' . uniqid());
    $items = $attributes->get('items', []);
    $selectable = $attributes->get('selectable', false);
    $collapsible = $attributes->get('collapsible', true);
    $selectedValue = $attributes->get('selected-value', null);
    $checkbox = $attributes->get('checkbox', false);

    $renderTreeItems = null;
    $renderTreeItems = function($items, $level = 0) use (&$renderTreeItems, $selectable, $checkbox, $selectedValue) {
        $html = '';
        foreach ($items as $item) {
            $label = is_array($item) ? ($item['label'] ?? $item['name'] ?? '') : $item;
            $value = is_array($item) ? ($item['value'] ?? $item['id'] ?? $label) : $label;
            $children = is_array($item) ? ($item['children'] ?? []) : [];
            $hasChildren = !empty($children);
            $isSelected = $selectedValue && $value == $selectedValue;

            $html .= '<div' . ($hasChildren ? ' class="hs-accordion"' : '') . ' id="tree-' . md5($value . $level) . '">';

            if ($hasChildren) {
                $html .= '<button class="hs-accordion-toggle py-1.5 px-2 w-full flex items-center gap-x-2 text-start text-sm text-gray-800 rounded-lg hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:text-neutral-200 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700" aria-expanded="false">';
                $html .= '<svg class="hs-accordion-active:rotate-90 shrink-0 size-3.5 transition" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>';
                if ($checkbox) {
                    $html .= '<input type="checkbox" class="shrink-0 border-gray-300 rounded text-blue-600 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-600" value="' . e($value) . '"' . ($isSelected ? ' checked' : '') . '>';
                }
                $html .= '<span>' . e($label) . '</span>';
                $html .= '</button>';
                $html .= '<div class="hs-accordion-content w-full overflow-hidden transition-[height] duration-300 hidden" role="region">';
                $html .= '<div class="ms-4 ps-3 border-s border-gray-200 dark:border-neutral-700">';
                $html .= $renderTreeItems($children, $level + 1);
                $html .= '</div></div>';
            } else {
                $html .= '<div class="py-1.5 px-2 flex items-center gap-x-2 text-sm text-gray-800 rounded-lg hover:bg-gray-100 dark:text-neutral-200 dark:hover:bg-neutral-700' . ($isSelected ? ' bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400' : '') . '">';
                $html .= '<svg class="shrink-0 size-3.5 text-gray-400 dark:text-neutral-600" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>';
                if ($checkbox) {
                    $html .= '<input type="checkbox" class="shrink-0 border-gray-300 rounded text-blue-600 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-600" value="' . e($value) . '"' . ($isSelected ? ' checked' : '') . '>';
                }
                $html .= '<span>' . e($label) . '</span>';
                $html .= '</div>';
            }
            $html .= '</div>';
        }
        return $html;
    };
@endphp

<div id="{{ $id }}" class="hs-accordion-group" role="tree" aria-orientation="vertical"
    data-hs-accordion-always-open="{{ $collapsible ? 'false' : 'true' }}"
    {{ $attributes->except(['items', 'selectable', 'collapsible', 'selected-value', 'checkbox']) }}
>
    {!! $renderTreeItems($items) !!}
</div>
