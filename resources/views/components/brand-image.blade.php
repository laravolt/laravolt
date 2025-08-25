<div data-role="x-brand-image" {{ $attributes->class('flex items-center justify-center') }}>
    @if ($isSvg)
        {!! $brandImage !!}
    @else
        <img src="{{ asset($brandImage) }}" alt="" class="h-11.5 w-auto" />
    @endif
</div>
