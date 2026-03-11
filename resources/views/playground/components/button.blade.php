<x-volt-panel title="Button">
    <x-volt-button label="Primary Button"></x-volt-button>
    <x-volt-button label="Secondary Button" class="secondary"></x-volt-button>
    <x-volt-button label="Basic Button" class="basic"></x-volt-button>

    <div class="border-t border-gray-200 dark:border-neutral-700 my-6"></div>

    <div class="flex items-center gap-x-2">
        <x-volt-button label="Primary Button" class="primary"></x-volt-button>
        <x-volt-button label="Basic Button" class="basic"></x-volt-button>
    </div>

    <div class="border-t border-gray-200 dark:border-neutral-700 my-6"></div>

    <div class="flex flex-wrap items-center gap-2">
        @foreach(config('laravolt.ui.colors') as $color => $hex)
            <x-volt-button class="{{ $color }}">{{ $color }}</x-volt-button>
        @endforeach
    </div>
    <div class="flex flex-wrap items-center gap-2 mt-4">
        @foreach(config('laravolt.ui.colors') as $color => $hex)
            <x-volt-button class="{{ $color }} secondary">{{ $color }}</x-volt-button>
        @endforeach
    </div>
    <div class="flex flex-wrap items-center gap-2 mt-4">
        @foreach(config('laravolt.ui.colors') as $color => $hex)
            <x-volt-button class="{{ $color }} basic">{{ $color }}</x-volt-button>
        @endforeach
    </div>
</x-volt-panel>
