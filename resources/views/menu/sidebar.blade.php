@php
/** @var \Laravolt\Platform\Services\SidebarMenu */
$items = app('laravolt.menu.sidebar')->allMenu();
@endphp

<aside id="hs-pro-sidebar" class="hs-overlay hs-overlay-open:translate-x-0 -translate-x-full transition-all duration-300 transform w-64 fixed top-0 start-0 bottom-0 z-[60] bg-white border-r border-gray-200 pt-14 dark:bg-neutral-800 dark:border-neutral-700 lg:translate-x-0 lg:end-auto lg:bottom-0">
    <div class="h-full overflow-y-auto overflow-x-hidden p-4">
        @include('laravolt::menu.sidebar_logo')
        @include('laravolt::menu.sidebar_profile')
        @include('laravolt::menu.sidebar_menu')
    </div>
</aside>
