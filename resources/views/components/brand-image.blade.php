<div data-role="x-brand-image" {{ $attributes }}>
    @if($isSvg)
        {!! $brandImage !!}
    @else
        <img
                src="{{ asset($brandImage) }}"
                alt=""
                class="mx-auto h-12 w-auto"
        >
    @endif
</div>
