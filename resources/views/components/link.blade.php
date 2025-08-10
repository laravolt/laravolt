<a href="{{ $url }}" class="inline-flex items-center gap-x-2 text-sm font-medium link-accent {{ $class ?? '' }}">
    @if($icon)
        <i class="{{ $icon }}"></i>
    @endif
    {{ $label ?? $slot }}
</a>
