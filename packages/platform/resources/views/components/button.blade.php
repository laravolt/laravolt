<a href="{{ $url }}" class="ui button {{ $type ?? $class ?? '' }}">
    @if($icon)
        <i class="icon plus"></i>
    @endif
        {{ $label }}
</a>
