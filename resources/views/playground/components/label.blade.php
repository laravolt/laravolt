<x-volt-panel title="Label">
    <div class="flex flex-wrap items-center gap-2">
        @foreach(config('laravolt.ui.colors') as $color => $hex)
            <x-volt-label :color="'solid '.$color">{{ $color }}</x-volt-label>
        @endforeach
    </div>
    <div class="flex flex-wrap items-center gap-2 mt-2">
        @foreach(config('laravolt.ui.colors') as $color => $hex)
            <x-volt-label :color="$color">{{ $color }}</x-volt-label>
        @endforeach
    </div>
    <div class="flex flex-wrap items-center gap-2 mt-2">
        @foreach(config('laravolt.ui.colors') as $color => $hex)
            <x-volt-label :color="'basic '.$color">{{ $color }}</x-volt-label>
        @endforeach
    </div>
    <div class="flex flex-wrap items-center gap-2 mt-2">
        @foreach(config('laravolt.ui.colors') as $color => $hex)
            <x-volt-label :color="'circular empty '.$color"></x-volt-label>
        @endforeach
    </div>
    <div class="my-4 border-t border-gray-200"></div>
    <div class="flex flex-wrap items-center gap-2 mt-2">
        @foreach(config('laravolt.ui.colors') as $color => $hex)
            <x-volt-label :color="'circular empty basic '.$color"></x-volt-label>
        @endforeach
    </div>
</x-volt-panel>
