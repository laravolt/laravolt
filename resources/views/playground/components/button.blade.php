<x-volt-panel title="Button">
    <div class="space-x-2">
        <x-volt-button label="Primary Button"></x-volt-button>
        <x-volt-button label="Secondary Button" class="bg-gray-100 text-gray-700 hover:bg-gray-200"></x-volt-button>
        <x-volt-button label="Basic Button" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50"></x-volt-button>
    </div>

    <div class="my-4 border-t border-gray-200"></div>

    <div class="flex items-center gap-2">
        <x-volt-button label="Primary Button"></x-volt-button>
        <x-volt-button label="Basic Button" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50"></x-volt-button>
    </div>

    <div class="my-4 border-t border-gray-200"></div>

    <div class="flex flex-wrap items-center gap-2">
        @foreach(config('laravolt.ui.colors') as $color => $hex)
            <x-volt-button class="bg-teal-600 hover:bg-teal-700">{{ $color }}</x-volt-button>
        @endforeach
    </div>
    <div class="flex flex-wrap items-center gap-2 mt-2">
        @foreach(config('laravolt.ui.colors') as $color => $hex)
            <x-volt-button class="bg-gray-100 text-gray-700 hover:bg-gray-200">{{ $color }}</x-volt-button>
        @endforeach
    </div>
    <div class="flex flex-wrap items-center gap-2 mt-2">
        @foreach(config('laravolt.ui.colors') as $color => $hex)
            <x-volt-button class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">{{ $color }}</x-volt-button>
        @endforeach
    </div>
</x-volt-panel>
