<header class="sticky top-0 z-40 w-full border-b border-gray-200 bg-white" id="topbar">
    <div class="mx-auto flex h-12 items-center justify-between px-3">
        <div class="flex items-center gap-x-2">
            <button class="inline-flex items-center justify-center rounded-md p-2 hover:bg-gray-100" data-role="sidebar-visibility-switcher" type="button" aria-label="Toggle sidebar">
                <svg class="h-5 w-5 text-gray-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div class="pl-2 text-sm font-semibold text-gray-800" id="titlebar">
                {{ config('laravolt.ui.brand_name') }}
            </div>
        </div>

        <div class="flex items-center gap-x-2" id="userbar">
            @auth
                <div class="hs-dropdown [--trigger:click] relative inline-flex">
                    <button id="user-menu" type="button" class="hs-dropdown-toggle inline-flex items-center gap-x-2 rounded-full p-1.5 hover:bg-gray-100">
                        <img src="{{ auth()->user()->avatar }}" alt="" class="h-8 w-8 rounded-full object-cover">
                        <svg class="h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div class="hs-dropdown-menu hidden z-20 mt-2 w-48 rounded-xl border border-gray-200 bg-white p-2 shadow-md" aria-labelledby="user-menu">
                        <div class="px-2 py-1 text-xs font-medium text-gray-500">{{ auth()->user()->name }}</div>
                        <div class="my-1 h-px bg-gray-100"></div>
                        <a href="{{ route('my::profile.edit') }}" class="block rounded-md px-2 py-1.5 text-sm hover:bg-gray-50">@lang('Edit Profil')</a>
                        <a href="{{ route('my::password.edit') }}" class="block rounded-md px-2 py-1.5 text-sm hover:bg-gray-50">@lang('Edit Password')</a>
                        <div class="my-1 h-px bg-gray-100"></div>
                        <a href="{{ route('auth::logout') }}" class="block rounded-md px-2 py-1.5 text-sm hover:bg-gray-50" up-target="body">Logout</a>
                    </div>
                </div>
            @endauth
        </div>
    </div>
</header>
