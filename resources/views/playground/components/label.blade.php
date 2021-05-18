<x-volt-panel title="Label">
    <div class="ui horizontal list">
        @foreach(config('laravolt.ui.colors') as $color => $hex)
            <div class="item">
                <x-volt-label :color="'solid '.$color">{{ $color }}</x-volt-label>
            </div>
        @endforeach
    </div>
    <div class="ui horizontal list">
        @foreach(config('laravolt.ui.colors') as $color => $hex)
            <div class="item">
                <x-volt-label :color="$color">{{ $color }}</x-volt-label>
            </div>
        @endforeach
    </div>
    <div class="ui horizontal list">
        @foreach(config('laravolt.ui.colors') as $color => $hex)
            <div class="item">
                <x-volt-label :color="'basic '.$color">{{ $color }}</x-volt-label>
            </div>
        @endforeach
    </div>
    <div class="ui horizontal list">
        @foreach(config('laravolt.ui.colors') as $color => $hex)
            <div class="item">
                <x-volt-label :color="'circular empty '.$color"></x-volt-label>
            </div>
        @endforeach
    </div>
    <div class="divider"></div>
    <div class="ui horizontal list">
        @foreach(config('laravolt.ui.colors') as $color => $hex)
            <div class="item">
                <x-volt-label :color="'circular empty basic '.$color"></x-volt-label>
            </div>
        @endforeach
    </div>
</x-volt-panel>
