<div data-role="x-brand-image" {{ $attributes->merge(['class' => 'inline-flex items-center justify-center']) }}>
    @if ($isSvg)
        <div class="text-gray-900 dark:text-white">
            {!! $brandImage !!}
        </div>
    @else
        <img src="{{ asset($brandImage) }}" alt="" class="h-12 w-auto object-contain" />
    @endif
</div>
