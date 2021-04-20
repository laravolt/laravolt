<x-laravolt::panel title="Button">
    <x-laravolt::button label="Primary Button"></x-laravolt::button>
    <x-laravolt::button label="Secondary Button" class="secondary"></x-laravolt::button>
    <x-laravolt::button label="Basic Button" class="basic"></x-laravolt::button>

    <div class="ui divider section"></div>

    <div class="ui buttons">
        <x-laravolt::button label="Primary Button" class="primary"></x-laravolt::button>
        <x-laravolt::button label="Basic Button" class="basic"></x-laravolt::button>
    </div>

    <div class="ui divider section"></div>

    <div class="ui horizontal list">
        @foreach(config('laravolt.ui.colors') as $color => $hex)
            <div class="item">
                <x-laravolt::button class="{{ $color }}">{{ $color }}</x-laravolt::button>
            </div>
        @endforeach
    </div>
    <div class="ui horizontal list">
        @foreach(config('laravolt.ui.colors') as $color => $hex)
            <div class="item">
                <x-laravolt::button class="{{ $color }} secondary">{{ $color }}</x-laravolt::button>
            </div>
        @endforeach
    </div>
    <div class="ui horizontal list">
        @foreach(config('laravolt.ui.colors') as $color => $hex)
            <div class="item">
                <x-laravolt::button class="{{ $color }} basic">{{ $color }}</x-laravolt::button>
            </div>
        @endforeach
    </div>
</x-laravolt::panel>
