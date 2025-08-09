<a href="{{ $url }}" class="inline-flex items-center gap-x-2 rounded-md px-2 py-1.5 text-sm font-medium text-teal-700 hover:text-teal-800 hover:underline {{ $class ?? '' }}" themed>
    @if($icon)
        <x-volt-icon :name="$icon" />
    @endif
    {{ $label ?? $slot }}
</a>
