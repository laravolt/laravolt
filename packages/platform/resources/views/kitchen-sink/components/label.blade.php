<x-panel title="Label">
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
                <x-label :color="'secondary '.$color">{{ $color }}</x-label>
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
</x-panel>
