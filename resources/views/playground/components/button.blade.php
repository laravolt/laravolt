<x-volt-panel title="Button">
    <x-volt-button label="Primary Button"></x-volt-button>
    <x-volt-button label="Secondary Button" class="secondary"></x-volt-button>
    <x-volt-button label="Basic Button" class="basic"></x-volt-button>

    <div class="ui divider section"></div>

    <div class="ui buttons">
        <x-volt-button label="Primary Button" class="primary"></x-volt-button>
        <x-volt-button label="Basic Button" class="basic"></x-volt-button>
    </div>

    <div class="ui divider section"></div>

    <div class="ui horizontal list">
        @foreach(config('laravolt.ui.colors') as $color => $hex)
            <div class="item">
                <x-volt-button class="{{ $color }}">{{ $color }}</x-volt-button>
            </div>
        @endforeach
    </div>
    <div class="ui horizontal list">
        @foreach(config('laravolt.ui.colors') as $color => $hex)
            <div class="item">
                <x-volt-button class="{{ $color }} secondary">{{ $color }}</x-volt-button>
            </div>
        @endforeach
    </div>
    <div class="ui horizontal list">
        @foreach(config('laravolt.ui.colors') as $color => $hex)
            <div class="item">
                <x-volt-button class="{{ $color }} basic">{{ $color }}</x-volt-button>
            </div>
        @endforeach
    </div>
</x-volt-panel>
