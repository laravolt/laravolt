@php
    $id = $attributes->get('id', 'searchbox-' . uniqid());
    $placeholder = $attributes->get('placeholder', 'Search...');
    $value = $attributes->get('value', null);
    $action = $attributes->get('action', null);
    $name = $attributes->get('name', 'q');
    $size = $attributes->get('size', 'md');
    $autofocus = $attributes->get('autofocus', false);
    $shortcutKey = $attributes->get('shortcut-key', null);
    $sizeClasses = [
        'sm' => 'py-1.5 ps-8 pe-3 text-sm',
        'md' => 'py-2 ps-10 pe-4 text-sm',
        'lg' => 'py-2.5 ps-11 pe-5 text-base',
    ];
    $iconSizeClasses = [
        'sm' => 'size-3.5 start-2.5',
        'md' => 'size-4 start-3',
        'lg' => 'size-5 start-3.5',
    ];
    $currentSize = $sizeClasses[$size] ?? $sizeClasses['md'];
    $currentIconSize = $iconSizeClasses[$size] ?? $iconSizeClasses['md'];
@endphp

<div class="relative" {{ $attributes->except(['value', 'action', 'size', 'autofocus', 'shortcut-key']) }}>
    @if($action)
    <form action="{{ $action }}" method="GET">
    @endif

    <div class="relative">
        <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-3">
            <svg class="shrink-0 {{ $currentIconSize }} text-gray-400 dark:text-neutral-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
        </div>
        <input
            type="search"
            id="{{ $id }}"
            name="{{ $name }}"
            class="{{ $currentSize }} block w-full border-gray-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
            placeholder="{{ $placeholder }}"
            value="{{ $value }}"
            {{ $autofocus ? 'autofocus' : '' }}
        >
        @if($shortcutKey)
            <div class="absolute inset-y-0 end-0 flex items-center pointer-events-none pe-3">
                <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 bg-gray-100 border border-gray-200 text-xs font-mono text-gray-500 rounded dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400">
                    <kbd class="text-xs">{{ $shortcutKey }}</kbd>
                </span>
            </div>
        @endif
    </div>

    @if($action)
    </form>
    @endif
</div>

@if($shortcutKey)
@pushOnce('script')
<script>
document.addEventListener('keydown', function(e) {
    if (e.key === '/' && !['INPUT','TEXTAREA','SELECT'].includes(document.activeElement.tagName)) {
        e.preventDefault();
        document.getElementById('{{ $id }}')?.focus();
    }
});
</script>
@endPushOnce
@endif
