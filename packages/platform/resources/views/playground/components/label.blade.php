<x-laravolt::panel title="Label">
    <div class="ui horizontal list">
        @foreach(config('laravolt.ui.colors') as $color => $hex)
            <div class="item">
                <x-laravolt::label :color="'solid '.$color">{{ $color }}</x-laravolt::label>
            </div>
        @endforeach
    </div>
    <div class="ui horizontal list">
        @foreach(config('laravolt.ui.colors') as $color => $hex)
            <div class="item">
                <x-laravolt::label :color="$color">{{ $color }}</x-laravolt::label>
            </div>
        @endforeach
    </div>
    <div class="ui horizontal list">
        @foreach(config('laravolt.ui.colors') as $color => $hex)
            <div class="item">
                <x-laravolt::label :color="'basic '.$color">{{ $color }}</x-laravolt::label>
            </div>
        @endforeach
    </div>
    <div class="ui horizontal list">
        @foreach(config('laravolt.ui.colors') as $color => $hex)
            <div class="item">
                <x-laravolt::label :color="'circular empty '.$color"></x-laravolt::label>
            </div>
        @endforeach
    </div>
    <div class="divider"></div>
    <div class="ui horizontal list">
        @foreach(config('laravolt.ui.colors') as $color => $hex)
            <div class="item">
                <x-laravolt::label :color="'circular empty basic '.$color"></x-laravolt::label>
            </div>
        @endforeach
    </div>
</x-laravolt::panel>
