<a href="{{ $url }}" class="ui button link {{ $class ?? '' }}" themed>
    @if($icon)
        <i class="icon {{ $icon }}"></i>
    @endif
        {{ $label ?? $slot }}
</a>
