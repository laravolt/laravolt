<div class="text-center" data-role="">
    @if(strlen(config('laravolt.ui.brand_image')) > 0)
        <img src="{{ config('laravolt.ui.brand_image') }}" alt="" class="ui image centered {{ $class ?? 'tiny' }}">
    @endif

    <h1 class="m-0 ui header">
        {{ config('laravolt.ui.brand_name') }}
        <div class="sub header">{{ config('laravolt.ui.brand_description') }}</div>
    </h1>
</div>
