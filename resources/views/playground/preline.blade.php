<!DOCTYPE html>
<html lang="{{ config('app.locale') }}" class="relative min-h-full">

<head>
    <title>{{ $title ?? '' }} | {{ config('app.name') }}</title>

    <meta charset="UTF-8" />
    <meta http-equiv="x-ua-compatible" content="IE=edge, chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <meta content="no-cache">

    @stack('meta')

    @laravoltStyles
    @livewireStyles

    @stack('style')
    @stack('head')

    @laravoltScripts
</head>

<body
    class="bg-gray-50 dark:bg-neutral-900 text-gray-800 dark:text-neutral-200 {{ $bodyClass ?? '' }} @yield('body.class')">
    @include('laravolt::menu.topbar')
    @include('laravolt::menu.sidebar')

    {{-- <div class="w-full pt-14 lg:ps-64" up-main="root"> --}}
    {{-- @include('laravolt::menu.actionbar') --}}

    <main id="content" class="lg:ps-65 pt-15 pb-10 sm:pb-16">
        <!-- Breadcrumb -->
        <ol class="lg:hidden pt-3 md:pt-5 sm:pb-2 md:pb-0 px-2 sm:px-5 flex items-center whitespace-nowrap">
            <li class="flex items-center text-sm text-gray-600 dark:text-neutral-500">
                Dashboard
                <svg class="shrink-0 overflow-visible size-4 ms-1.5 text-gray-400 dark:text-neutral-600" width="16"
                    height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"
                    aria-hidden="true">
                    <path d="M6 13L10 3" stroke="currentColor" stroke-linecap="round"></path>
                </svg>
            </li>
            <li
                class="ps-1.5 flex items-center truncate font-semibold text-gray-800 dark:text-neutral-200 text-sm truncate">
                <span class="truncate">Overview</span>
            </li>
        </ol>
        <!-- End Breadcrumb -->

        <div class="p-2 sm:p-5 sm:py-0 md:pt-5 space-y-5">
            <div class="max-w-3xl mx-auto">
                <div class="space-y-4">
                    <div
                        class="p-5 bg-white border border-gray-200 shadow-2xs rounded-xl dark:bg-neutral-800 dark:border-neutral-700">
                        <div class="grid sm:grid-cols-12 gap-4">
                            <div class="sm:col-span-5 sm:order-2">
                                <div class="bg-gray-100 rounded-xl overflow-hidden dark:bg-neutral-700">
                                    <img class="ps-5 w-full" src="/laravolt/assets/images/analytics-demo.svg"
                                        alt="Template Image">
                                </div>
                            </div>
                            <!-- End Col -->

                            <div class="sm:col-span-7 sm:order-1">
                                <div class="h-full flex flex-col justify-between space-y-3">
                                    <div>
                                        <h3
                                            class="text-lg sm:text-lg font-semibold text-gray-800 dark:text-neutral-200">
                                            Digital empowerment platform.
                                        </h3>
                                        <p class="mt-1 sm:mt-3 text-gray-500 dark:text-neutral-500">
                                            Build sustainable information systems with Laravel, battle-tested
                                            components, and years of experience facing South East Asia's unique
                                            technology landscape.
                                        </p>
                                    </div>
                                    <p>
                                        <a class="inline-flex items-center gap-x-1 text-sm text-blue-600 decoration-2 hover:underline font-medium focus:outline-hidden focus:underline dark:text-blue-400 dark:hover:text-blue-500"
                                            href="https://laravolt.dev" target="_blank" rel="noopener noreferrer">
                                            Learn more about Laravolt
                                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg"
                                                width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path d="m9 18 6-6-6-6" />
                                            </svg>
                                        </a>
                                    </p>
                                </div>
                            </div>
                            <!-- End Col -->
                        </div>
                    </div>
                    <!-- End Card -->
                </div>
            </div>
        </div>
    </main>
    {{-- </div> --}}

    @livewireScripts
    @stack('script')
    @stack('body')
</body>

</html>
