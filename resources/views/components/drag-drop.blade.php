@php
    $id = $id ?? 'drag-drop-' . uniqid();
    $group = $group ?? 'default';
    $sortable = $sortable ?? true;
    $handle = $handle ?? null;
@endphp

<div id="{{ $id }}" class="space-y-2" data-sortable-group="{{ $group }}" {{ $attributes->except(['group', 'sortable', 'handle']) }}>
    {{ $slot }}
</div>

@pushOnce('drag-drop-scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-sortable-group]').forEach(function(el) {
        new Sortable(el, {
            group: el.dataset.sortableGroup || 'default',
            animation: 150,
            ghostClass: 'opacity-30',
            chosenClass: 'ring-2 ring-blue-500',
            handle: '{{ $handle ?? '' }}' || undefined,
            onEnd: function(evt) {
                el.dispatchEvent(new CustomEvent('sort-changed', {
                    detail: {
                        oldIndex: evt.oldIndex,
                        newIndex: evt.newIndex,
                        item: evt.item,
                    },
                    bubbles: true,
                }));
            }
        });
    });
});
</script>
@endPushOnce
