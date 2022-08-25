<div data-role="x-brand-image" {{ $attributes }}>
    @if($isSvg)
        {!! $brandImage !!}
    @else
        <img
                src="{{ asset($brandImage) }}"
                alt=""
                class="ui image small centered"
        >
    @endif
</div>
