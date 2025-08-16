<div {{ $attributes->merge(['class' => 'p-5 md:p-8 bg-white border border-gray-200 shadow-2xs rounded-xl dark:bg-neutral-800 dark:border-neutral-700']) }}>
    @php
        $icon = $icon ?? false;
    @endphp

    @if($title or $icon)
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-4 xl:mb-8">
            <!-- Title -->
            <div>
                @if ($title)
                    <h1 class="text-lg font-semibold text-gray-800 dark:text-neutral-200">
                        @if($icon)
                            <div class="text-gray-600 dark:text-neutral-300">
                                <x-volt-icon :name="$icon" :class="$iconClass"/>
                            </div>
                        @endif
                        {{ $title }}
                    </h1>
                @endif
                @if ($description)
                    <p class="text-sm text-gray-500 dark:text-neutral-500">
                        {{ $description }}
                    </p>
                @endif
            </div>
            <!-- End Title -->

            @if (isset($action))
                <div class="relative">{!! $action !!}</div>
            @endif
        </div>
    @endif

    <div>
        {!! $slot !!}
    </div>

    @if(isset($footer))
        <div class="px-4 py-3 border-t border-gray-200 dark:border-neutral-700">
            {!! $footer !!}
        </div>
    @endif
</div>
