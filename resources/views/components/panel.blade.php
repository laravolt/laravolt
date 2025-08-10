@php($icon = $icon ?? false)

<div {{ $attributes->merge(['class' => 'rounded-xl border border-gray-200 shadow-sm dark:border-neutral-700']) }}>
    @if($title or $icon)
        <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between dark:border-neutral-700 {{ $attributes['headerClass'] ?? '' }}">
            <div class="flex items-center gap-x-2 min-h-0">
                @if($icon)
                    <div class="text-gray-600 dark:text-neutral-300">
                        <x-volt-icon :name="$icon" :class="$iconClass"/>
                    </div>
                @endif
                @if($title)
                    <div>
                        <h4 class="text-base font-semibold text-gray-800 dark:text-neutral-200">
                            {!! $title !!}
                        </h4>
                        @if($description)
                            <div class="text-sm text-gray-500 dark:text-neutral-400">{!! $description !!}</div>
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
    @endif

    <div class="p-4 {{ $attributes['contentClass'] ?? '' }}">
        {!! $slot !!}
    </div>

    @if(isset($footer))
        <div class="px-4 py-3 border-t border-gray-200 dark:border-neutral-700">
            {!! $footer !!}
        </div>
    @endif
</div>
