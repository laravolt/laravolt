@php($icon = $icon ?? false)

<div class="ui segments panel" {{ $attributes }}>
    @if($title or $icon)
        <div class="ui segment panel__header {{ $attributes['headerClass'] ?? '' }}">
            <div class="ui horizontal list m-0">
                @if($icon)
                    <div class="item p-0 p-l-xs m-0">
                        <i class="icon {{ $icon }}"></i>
                    </div>
                @endif

                @if($title)
                    <div class="item p-0 m-0">
                        <h4 class="ui header p-x-sm p-y-0">
                            {!! $title !!}
                        </h4>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <div class="ui segment {{ $attributes['contentClass'] ?? 'p-2' }}">
        {!! $slot !!}
    </div>

    @if(isset($footer))
        <div class="ui segment">
            footer
        </div>
    @endif
</div>
