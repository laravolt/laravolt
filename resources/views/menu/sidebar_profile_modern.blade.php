@auth
<!-- Profile Section -->
<div class="p-4 border-b border-gray-200 dark:border-gray-700">
    <div class="flex items-center">
        <div class="shrink-0">
            <img class="size-10 rounded-full" 
                 src="{{ auth()->user()->avatar ?? '/images/default-avatar.png' }}" 
                 alt="{{ auth()->user()->name }}">
        </div>
        <div class="ms-3 overflow-hidden">
            <p class="text-sm font-medium text-gray-800 dark:text-white truncate">
                {{ auth()->user()->name }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                {{ auth()->user()->email }}
            </p>
        </div>
    </div>
</div>
@endauth