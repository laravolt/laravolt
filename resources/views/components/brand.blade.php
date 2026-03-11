<div class="text-center" data-role="">
    @if(strlen(config('laravolt.ui.brand_image')) > 0)
        <img src="{{ config('laravolt.ui.brand_image') }}" alt="" class="mx-auto block {{ $class ?? 'w-16 h-16' }}">
    @endif

    <h1 class="m-0 text-2xl font-bold text-gray-800 dark:text-white">
        {{ config('laravolt.ui.brand_name') }}
        <div class="text-sm font-normal text-gray-500 dark:text-neutral-400">{{ config('laravolt.ui.brand_description') }}</div>
    </h1>
</div>
