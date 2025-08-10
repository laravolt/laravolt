<header class="lg:ms-64 fixed top-0 inset-x-0 flex flex-wrap md:justify-start md:flex-nowrap z-50 bg-white border-b border-gray-200 dark:bg-neutral-800 dark:border-neutral-700" id="topbar">
    <div class="flex justify-between xl:grid xl:grid-cols-3 basis-full items-center w-full py-2.5 px-2 sm:px-5">
        <div class="xl:col-span-1 flex items-center md:gap-x-3">
            <div class="lg:hidden">
                <button type="button" class="w-7 h-9 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 focus:outline-hidden focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700" aria-haspopup="dialog" aria-expanded="false" aria-controls="hs-pro-sidebar" aria-label="Toggle navigation" data-hs-overlay="#hs-pro-sidebar">
                    <svg class="shrink-0 h-4 w-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 8L21 12L17 16M3 12H13M3 6H13M3 18H13" />
                    </svg>
                </button>
            </div>

            <div class="hidden lg:block min-w-80 xl:w-full">
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none z-20 ps-3.5">
                        <svg class="shrink-0 h-4 w-4 text-gray-400 dark:text-white/60" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.3-4.3" />
                        </svg>
                    </div>
                    <input type="text" class="py-2 ps-10 pe-16 block w-full bg-white border-gray-200 rounded-lg text-sm focus:outline-hidden focus:ring-0 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder:text-neutral-400 dark:focus:ring-neutral-600" placeholder="Search" />
                    <div class="absolute inset-y-0 end-0 flex items-center pointer-events-none z-20 pe-3 text-gray-400">
                        <svg class="shrink-0 h-3 w-3 text-gray-400 dark:text-white/60" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 6v12a3 3 0 1 0 3-3H6a3 3 0 1 0 3 3V6a3 3 0 1 0-3 3h12a3 3 0 1 0-3-3" />
                        </svg>
                        <span class="mx-1">
                            <svg class="shrink-0 h-3 w-3 text-gray-400 dark:text-white/60" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12h14" />
                                <path d="M12 5v14" />
                            </svg>
                        </span>
                        <span class="text-xs">/</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="xl:col-span-2 flex justify-end items-center gap-x-2" id="userbar">
            @auth
                <div class="hs-dropdown [--placement:bottom-right] relative inline-flex">
                    <button id="user-menu" type="button" class="hs-dropdown-toggle h-9 w-9 inline-flex justify-center items-center gap-x-2 rounded-full border border-transparent text-gray-500 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none focus:outline-hidden focus:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700" aria-haspopup="menu" aria-expanded="false" aria-label="Dropdown">
                        <img src="{{ auth()->user()->avatar }}" alt="" class="h-9 w-9 rounded-full object-cover">
                        <svg class="shrink-0 h-4 w-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m6 9 6 6 6-6" />
                        </svg>
                    </button>
                    <div class="hs-dropdown-menu hs-dropdown-open:opacity-100 w-48 transition-[opacity,margin] duration opacity-0 hidden z-20 bg-white rounded-xl shadow-xl border border-gray-200 p-2 dark:bg-neutral-900 dark:border-neutral-800" role="menu" aria-orientation="vertical" aria-labelledby="user-menu">
                        <div class="px-2 py-1 text-xs font-medium text-gray-500 dark:text-neutral-400">{{ auth()->user()->name }}</div>
                        <div class="my-1 h-px bg-gray-100 dark:bg-neutral-800"></div>
                        <a href="{{ route('my::profile.edit') }}" class="block rounded-md px-2 py-1.5 text-sm hover:bg-gray-50 dark:hover:bg-neutral-800">@lang('Edit Profil')</a>
                        <a href="{{ route('my::password.edit') }}" class="block rounded-md px-2 py-1.5 text-sm hover:bg-gray-50 dark:hover:bg-neutral-800">@lang('Edit Password')</a>
                        <div class="my-1 h-px bg-gray-100 dark:bg-neutral-800"></div>
                        <a href="{{ route('auth::logout') }}" class="block rounded-md px-2 py-1.5 text-sm hover:bg-gray-50 dark:hover:bg-neutral-800" up-target="body">Logout</a>
                    </div>
                </div>
            @endauth
        </div>
    </div>
</header>
