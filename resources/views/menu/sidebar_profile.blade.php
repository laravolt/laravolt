@auth
    <div class="flex items-center gap-x-3 p-3 rounded-lg bg-gray-50 dark:bg-neutral-700/30 mb-3">
        <img src="{{ auth()->user()->avatar }}" class="size-10 rounded-full" alt="avatar">
        <div>
            <div class="text-sm font-medium text-gray-800 dark:text-neutral-200">{{ auth()->user()->name }}</div>
            <a href="{{ route('auth::logout') }}" class="text-xs text-gray-500 hover:text-blue-600 dark:text-neutral-400 dark:hover:text-blue-400">Logout</a>
        </div>
    </div>
@endauth
