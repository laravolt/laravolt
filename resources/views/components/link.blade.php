<a href="{{ $url }}" class="inline-flex items-center gap-x-2 text-sm font-medium text-blue-600 hover:underline focus:outline-hidden focus:underline dark:text-blue-500 {{ $class ?? '' }}">
    @if($icon)
        <i class="{{ $icon }}"></i>
    @endif
    {{ $label ?? $slot }}
</a>
