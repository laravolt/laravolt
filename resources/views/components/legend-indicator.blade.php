@php
    $items = $attributes->get('items', []);
    $layout = $attributes->get('layout', 'horizontal');
    $layoutClass = $layout === 'vertical' ? 'flex-col space-y-2' : 'flex-wrap gap-x-4 gap-y-1';
@endphp

<div class="flex {{ $layoutClass }}" {{ $attributes->except(['items', 'layout']) }}>
    @foreach($items as $item)
        @php
            $label = is_array($item) ? ($item['label'] ?? '') : $item;
            $color = is_array($item) ? ($item['color'] ?? 'bg-blue-600') : 'bg-blue-600';
            $value = is_array($item) ? ($item['value'] ?? null) : null;
        @endphp
        <div class="flex items-center gap-x-2">
            <span class="inline-block size-2.5 rounded-full {{ $color }}"></span>
            <span class="text-sm text-gray-600 dark:text-neutral-400">
                {{ $label }}
                @if($value !== null)
                    <span class="font-semibold text-gray-800 dark:text-neutral-200">{{ $value }}</span>
                @endif
            </span>
        </div>
    @endforeach
</div>
