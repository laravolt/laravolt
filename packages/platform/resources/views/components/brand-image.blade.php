@if(strlen(config('laravolt.ui.brand_image')) > 0)
    <img src="{{ config('laravolt.ui.brand_image') }}" alt="" class="ui image {{ $class ?? 'tiny' }}">
@endif
