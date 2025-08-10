<header id="topbar" class="lg:ms-64 fixed top-0 inset-x-0 z-50 bg-white border-b border-gray-200 dark:bg-neutral-800 dark:border-neutral-700">
    <div class="flex items-center justify-between w-full py-2.5 px-3 sm:px-5">
        <div class="flex items-center gap-x-2">
            <button type="button" class="lg:hidden w-7 h-9.5 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-2xs hover:bg-gray-50 focus:outline-hidden focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700" aria-haspopup="dialog" aria-expanded="false" aria-controls="hs-pro-sidebar" aria-label="Toggle navigation" data-hs-overlay="#hs-pro-sidebar">
                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div class="hidden sm:block text-sm text-gray-600 dark:text-neutral-300">{{ config('laravolt.ui.brand_name') }}</div>
        </div>

        <div class="flex items-center gap-x-2" id="userbar">
            @auth
            <div class="hs-dropdown [--placement:bottom-right] relative inline-flex">
                <button id="hs-user-menu" type="button" class="size-9.5 inline-flex justify-center items-center gap-x-2 rounded-full border border-transparent text-gray-500 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none focus:outline-hidden focus:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700" aria-haspopup="menu" aria-expanded="false">
                    <img src="{{ auth()->user()->avatar }}" alt="" class="size-8 rounded-full">
                </button>
                <div class="hs-dropdown-menu hs-dropdown-open:opacity-100 w-56 transition-[opacity,margin] duration opacity-0 hidden z-10 bg-white rounded-xl shadow-xl dark:bg-neutral-900" role="menu" aria-orientation="vertical" aria-labelledby="hs-user-menu">
                    <div class="p-1">
                        <div class="px-3 py-2 text-sm text-gray-600 dark:text-neutral-300">{{ auth()->user()->name }}</div>
                        <div class="my-1 border-t border-gray-200 dark:border-neutral-800"></div>
                        <a href="{{ route('my::profile.edit') }}" class="flex gap-x-3 py-2 px-3 rounded-lg text-sm text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800">@lang('Edit Profil')</a>
                        <a href="{{ route('my::password.edit') }}" class="flex gap-x-3 py-2 px-3 rounded-lg text-sm text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800">@lang('Edit Password')</a>
                        <div class="my-1 border-t border-gray-200 dark:border-neutral-800"></div>
                        <a href="{{ route('auth::logout') }}" class="flex gap-x-3 py-2 px-3 rounded-lg text-sm text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800" up-target="body">Logout</a>
                    </div>
                </div>
            </div>
            @endauth
        </div>
    </div>
</header>
