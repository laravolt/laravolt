<div data-role="x-brand-image" {{ $attributes }}>
    @if($isSvg)
        {!! $brandImage !!}
    @else
        <img
                src="{{ $brandImage }}"
                alt=""
                class="ui image tiny centered"
        >
    @endif
</div>
