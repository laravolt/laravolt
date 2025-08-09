@php($icon = $icon ?? false)

<div {{ $attributes->merge(['class' => 'bg-white border border-gray-200 rounded-xl shadow-sm']) }}>
    @if($title or $icon)
        <div class="px-4 py-3 border-b border-gray-200 {{ $attributes['headerClass'] ?? '' }}">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-x-2">
                    @if($icon)
                        <div class="text-teal-600">
                            <x-volt-icon :name="$icon" :class="$iconClass"/>
                        </div>
                    @endif
                    @if($title)
                        <div>
                            <h4 class="text-base font-semibold text-gray-800">
                                {!! $title !!}
                            </h4>
                            @if($description)
                                <p class="mt-0.5 text-sm text-gray-500">{!! $description !!}</p>
                            @endif
                        </div>
                    @endif
                </div>
                @if(isset($action))
                    <div>
                        {!! $action !!}
                    </div>
                @endif
            </div>
        </div>
    @endif

    <div class="px-4 py-4 {{ $attributes['contentClass'] ?? '' }}">
        {!! $slot !!}
    </div>

    @if(isset($footer))
        <div class="px-4 py-3 border-t border-gray-200">
            {!! $footer !!}
        </div>
    @endif
</div>
