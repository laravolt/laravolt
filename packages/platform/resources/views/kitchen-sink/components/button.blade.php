<x-panel title="Button">
    <x-button label="Primary Button"></x-button>
    <x-button label="Secondary Button" class="secondary"></x-button>
    <x-button label="Basic Button" class="basic"></x-button>

    <div class="ui divider section"></div>

    <div class="ui buttons">
        <x-button label="Primary Button" class="primary"></x-button>
        <x-button label="Basic Button" class="basic"></x-button>
    </div>

    <div class="ui divider section"></div>

    <div class="ui horizontal list">
        @foreach(config('laravolt.ui.colors') as $color => $hex)
            <div class="item">
                <x-button class="{{ $color }}">{{ $color }}</x-button>
            </div>
        @endforeach
    </div>
    <div class="ui horizontal list">
        @foreach(config('laravolt.ui.colors') as $color => $hex)
            <div class="item">
                <x-button class="{{ $color }} secondary">{{ $color }}</x-button>
            </div>
        @endforeach
    </div>
    <div class="ui horizontal list">
        @foreach(config('laravolt.ui.colors') as $color => $hex)
            <div class="item">
                <x-button class="{{ $color }} basic">{{ $color }}</x-button>
            </div>
        @endforeach
    </div>
</x-panel>
