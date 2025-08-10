<div id="actionbar" class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 px-4 sm:px-6 lg:px-8 py-3 border-b border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-800">
    <div>
        @yield('breadcrumb')
        <h3 class="text-lg font-semibold text-gray-800 dark:text-neutral-200">
            {{ $title }}
        </h3>
        @if(!empty($subtitle))
            <div class="text-sm text-gray-500 dark:text-neutral-400">{{ $subtitle }}</div>
        @endif
    </div>
    <div>
        {{ $actions ?? '' }}
    </div>
</div>
