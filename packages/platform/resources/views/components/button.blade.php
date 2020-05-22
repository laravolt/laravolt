<button type="{{ $type }}" class="ui button {{ $class }} rounded-full" themed>
    @if($icon)
        <i class="icon {{ $icon }}"></i>
    @endif
    {{ $label ?? $slot }}
</button>
