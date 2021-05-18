@php($icon = $icon ?? false)

<div {{ $attributes->merge(['class' => 'ui segments panel']) }}>
    @if($title or $icon)
        <div class="ui segment panel__header {{ $attributes['headerClass'] ?? '' }}">
            <div class="ui menu secondary borderless m-0 p-0" style="min-height: 0">
                @if($icon)
                    <div class="panel__icon item p-0 p-l-xs m-0">
                        <x-volt-icon :name="$icon" :class="$iconClass"/>
                    </div>
                @endif

                @if($title)
                    <div class="item p-0 m-0">
                        <h4 class="panel__title ui header p-x-sm p-y-0">
                            {!! $title !!}
                            @if($description)
                            <div class="sub header">{!! $description !!}</div>
                            @endif
                        </h4>
                    </div>
                @endif

                @if(isset($action))
                <div class="menu right">
                    <div class="item p-0">
                        {!! $action !!}
                    </div>
                </div>
                @endif

            </div>
        </div>
    @endif

    <div class="ui segment {{ $attributes['contentClass'] ?? 'p-3' }}">
        {!! $slot !!}
    </div>

    @if(isset($footer))
        <div class="ui segment">
            {!! $footer !!}
        </div>
    @endif
</div>
