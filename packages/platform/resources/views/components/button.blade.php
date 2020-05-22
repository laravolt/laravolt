<button type="{{ $type }}" {{ $attributes->merge(['class' => 'ui button '.$class]) }} themed>
    @if($icon)
        <i class="icon {{ $icon }}"></i>
    @endif
    {{ $label ?? $slot }}
</button>
