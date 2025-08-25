<div class="flex justify-between items-center gap-x-5">
    <div>
        <h2 class="inline-block text-lg font-semibold text-gray-800 dark:text-neutral-200">
            {{ $title ?? '' }}
        </h2>
        @if (!empty($subtitle))
            <div class="text-sm text-gray-500 dark:text-neutral-400">{{ $subtitle }}</div>
        @endif
    </div>

    @if (!empty($actions))
        <div class="flex justify-end items-center gap-x-2">
            {{ $actions }}
        </div>
    @endif
</div>
