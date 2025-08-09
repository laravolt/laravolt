<div id="actionbar" class="px-4 py-2 border-b border-gray-200 bg-white">
    <div class="flex items-center justify-between">
        <div>
            @yield('breadcrumb')
            <h3 class="mt-1 text-lg font-semibold text-gray-800">
                {{ $title }}
                <span class="block text-sm font-normal text-gray-500">{{ $subtitle ?? '' }}</span>
            </h3>
        </div>
        <div class="flex items-center gap-x-2">
            {{ $actions ?? '' }}
        </div>
    </div>
</div>
