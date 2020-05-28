<button {{ $attributes->merge(['class' => 'ui button '.$class]) }}>
    @if($icon)
        <i class="icon {{ $icon }}"></i>
    @endif
    {{ $label ?? $slot }}
</button>
