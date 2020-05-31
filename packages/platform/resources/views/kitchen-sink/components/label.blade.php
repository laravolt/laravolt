<x-panel title="Label">
    <div class="ui horizontal list">
        @foreach(config('laravolt.ui.colors') as $color => $hex)
            <div class="item">
                <x-label :color="'solid '.$color">{{ $color }}</x-label>
            </div>
        @endforeach
    </div>
    <div class="ui horizontal list">
        @foreach(config('laravolt.ui.colors') as $color => $hex)
            <div class="item">
                <x-label :color="$color">{{ $color }}</x-label>
            </div>
        @endforeach
    </div>
    <div class="ui horizontal list">
        @foreach(config('laravolt.ui.colors') as $color => $hex)
            <div class="item">
                <x-label :color="'basic '.$color">{{ $color }}</x-label>
            </div>
        @endforeach
    </div>
    <div class="ui horizontal list">
        @foreach(config('laravolt.ui.colors') as $color => $hex)
            <div class="item">
                <x-label :color="'circular empty '.$color"></x-label>
            </div>
        @endforeach
    </div>
    <div class="divider"></div>
    <div class="ui horizontal list">
        @foreach(config('laravolt.ui.colors') as $color => $hex)
            <div class="item">
                <x-label :color="'circular empty basic '.$color"></x-label>
            </div>
        @endforeach
    </div>
</x-panel>
