<x-volt-panel :title="$this->title()">
    <div class="flex items-center gap-x-4">
        @if($this->icon())
            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-teal-50 text-teal-600">
                <x-volt-icon :name="$this->icon()"></x-volt-icon>
            </div>
        @endif
        <div class="flex-1">
            <div class="w-full">
                <div class="text-2xl font-semibold text-gray-800">
                    {{ $this->value() }}
                </div>
                <div class="text-sm">
                    <span class="text-teal-600">
                        {{ $this->label() }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</x-volt-panel>
