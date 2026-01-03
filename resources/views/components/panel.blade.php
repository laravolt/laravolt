@php
    $icon = $icon ?? false;
    $headerClass = $attributes['headerClass'] ?? '';
    $contentClass = $attributes['contentClass'] ?? 'p-6';
    $attributes = $attributes->except(['headerClass', 'contentClass']);

    $panelClasses = 'bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-neutral-800 dark:border-neutral-700';
@endphp

<div {{ $attributes->merge(['class' => $panelClasses]) }}>
    @if($title || $icon)
        <div class="border-b border-gray-200 px-6 py-4 {{ $headerClass }} dark:border-neutral-700">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-x-3">
                    @if($icon)
                        <div class="flex-shrink-0">
                            <x-volt-icon :name="$icon" :class="$iconClass ?? 'w-5 h-5 text-gray-600 dark:text-neutral-400'"/>
                        </div>
                    @endif

                    @if($title)
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {!! $title !!}
                            </h3>
                            @if($description)
                                <p class="text-sm text-gray-500 dark:text-neutral-400 mt-1">
                                    {!! $description !!}
                                </p>
                            @endif
                        </div>
                    @endif
                </div>

                @if(isset($action))
                    <div class="flex-shrink-0">
                        {!! $action !!}
                    </div>
                @endif
            </div>
        </div>
    @endif

    <div class="{{ $contentClass }}">
        {!! $slot !!}
    </div>

    @if(isset($footer))
        <div class="border-t border-gray-200 px-6 py-4 dark:border-neutral-700">
            {!! $footer !!}
        </div>
    @endif
</div>
