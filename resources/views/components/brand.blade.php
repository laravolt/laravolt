<div class="text-center" data-role="">
    @if(strlen(config('laravolt.ui.brand_image')) > 0)
        <img src="{{ config('laravolt.ui.brand_image') }}" alt="" class="mx-auto h-10 w-auto {{ $class ?? '' }}">
    @endif

    <h1 class="m-0 text-xl font-semibold text-gray-800">
        {{ config('laravolt.ui.brand_name') }}
        <span class="block text-sm font-normal text-gray-500">{{ config('laravolt.ui.brand_description') }}</span>
    </h1>
</div>
