@auth
    <div class="flex items-center gap-x-3 px-4 py-3">
        <img src="{{ auth()->user()->avatar }}" class="shrink-0 size-10 rounded-full" alt="{{ auth()->user()->name }}">
        <div class="grow min-w-0">
            <h4 class="text-sm font-semibold text-gray-800 truncate dark:text-neutral-200">{{ auth()->user()->name }}</h4>
            <a href="{{ route('auth::logout') }}" class="text-xs text-gray-500 hover:text-blue-600 dark:text-neutral-400 dark:hover:text-blue-400">Logout</a>
        </div>
    </div>
@endauth
