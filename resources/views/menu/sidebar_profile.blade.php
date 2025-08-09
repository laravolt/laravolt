@auth
    <div class="flex items-center gap-x-3 p-3">
        <img src="{{ auth()->user()->avatar }}" class="h-10 w-10 rounded-full object-cover">
        <div class="min-w-0">
            <h4 class="truncate text-sm font-medium text-gray-800">{{ auth()->user()->name }}</h4>
            <div>
                <a href="{{ route('auth::logout') }}" class="text-xs text-gray-500 hover:text-gray-700">Logout</a>
            </div>
        </div>
    </div>
@endauth
