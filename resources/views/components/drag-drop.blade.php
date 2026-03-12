@php
    $id = $attributes->get('id', 'drag-drop-' . uniqid());
    $group = $attributes->get('group', 'default');
    $sortable = $attributes->get('sortable', true);
    $handle = $attributes->get('handle', null);
@endphp

<div id="{{ $id }}" class="space-y-2" data-sortable-group="{{ $group }}" {{ $attributes->except(['group', 'sortable', 'handle']) }}>
    {{ $slot }}
</div>

@pushOnce('script')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-sortable-group]').forEach(function(el) {
        new Sortable(el, {
            group: el.dataset.sortableGroup || 'default',
            animation: 150,
            ghostClass: 'opacity-30',
            handle: el.dataset.sortableHandle || undefined,
            onChoose: function(evt) {
                evt.item.classList.add('ring-2', 'ring-blue-500');
            },
            onUnchoose: function(evt) {
                evt.item.classList.remove('ring-2', 'ring-blue-500');
            },
            onEnd: function(evt) {
                evt.item.classList.remove('ring-2', 'ring-blue-500');
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
