<x-panel title="Button">
    <x-button label="Primary Button" type="primary"></x-button>
    <x-button label="Secondary Button" type="secondary"></x-button>
    <x-button label="Basic Button" type="basic"></x-button>

    <div class="ui divider section"></div>

    <div class="ui horizontal list">
        @foreach(config('laravolt.ui.colors') as $color => $hex)
            <div class="item">
                <x-button type="{{ $color }}">{{ $color }}</x-button>
            </div>
        @endforeach
    </div>
    <div class="ui horizontal list">
        @foreach(config('laravolt.ui.colors') as $color => $hex)
            <div class="item">
                <x-button type="{{ $color }} secondary">{{ $color }}</x-button>
            </div>
        @endforeach
    </div>
    <div class="ui horizontal list">
        @foreach(config('laravolt.ui.colors') as $color => $hex)
            <div class="item">
                <x-button type="{{ $color }} basic">{{ $color }}</x-button>
            </div>
        @endforeach
    </div>
</x-panel>
