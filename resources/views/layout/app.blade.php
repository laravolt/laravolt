@php
    $title = $title
        ?? (isset($module)
            ? $module->getLabel()
            : config('app.name'));
    $isShowTitleBar = isset($isShowTitleBar) ? filter_var($isShowTitleBar, FILTER_VALIDATE_BOOLEAN) : true;
@endphp

<x-volt-base :title="$title">
    @include('laravolt::menu.topbar')
    @include('laravolt::menu.sidebar')

    <main id="content" class="lg:ps-65 pt-15 pb-10 sm:pb-16">
        <div class="p-2 sm:p-5 sm:py-0 md:pt-5 space-y-5">
            @if ($isShowTitleBar)
                <div class="flex justify-between items-center gap-x-5">
                    <h2 class="inline-block text-lg font-semibold text-gray-800 dark:text-neutral-200">
                        {{ $title }}
                    </h2>

                    @if (isset($headerActions))
                        <div class="flex justify-end items-center gap-x-2">
                            {{ $headerActions }}
                        </div>
                    @endif
                </div>
            @endif

            {{ $slot ?? null }}
        </div>
    </main>

    <footer class="lg:ps-65 h-10 sm:h-16 absolute bottom-0 inset-x-0">
        <div class="p-2 sm:p-5 flex justify-between items-center">
            <p class="text-xs sm:text-sm text-gray-500 dark:text-neutral-200">
                &copy; {{ now()->year }} {{ config('app.name') }}.
            </p>
        </div>
    </footer>
</x-volt-base>
