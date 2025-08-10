<div class="text-center" data-role="">
    @if(strlen(config('laravolt.ui.brand_image')) > 0)
        <img src="{{ config('laravolt.ui.brand_image') }}" alt="" class="mx-auto {{ $class ?? '' }} h-10 w-auto">
    @endif

    <h1 class="mt-3 text-2xl font-semibold text-gray-800 dark:text-white">
        {{ config('laravolt.ui.brand_name') }}
        <div class="mt-1 text-sm font-normal text-gray-500 dark:text-neutral-400">{{ config('laravolt.ui.brand_description') }}</div>
    </h1>
</div>
