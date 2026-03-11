<x-volt-panel :title="$this->title()">
    <div class="flex items-center gap-x-4">
        @if($this->icon())
            <div class="flex-shrink-0">
                <span class="inline-flex items-center justify-center size-12 rounded-xl bg-{{ $this->color() }}-100 text-{{ $this->color() }}-600 dark:bg-{{ $this->color() }}-900/50 dark:text-{{ $this->color() }}-400">
                    <x-volt-icon :name="$this->icon()"></x-volt-icon>
                </span>
            </div>
        @endif
        <div class="grow">
            <p class="text-3xl font-bold text-gray-800 dark:text-white">
                {{ $this->value() }}
            </p>
            <p class="text-sm font-medium text-{{ $this->color() }}-600 dark:text-{{ $this->color() }}-400">
                {{ $this->label() }}
            </p>
        </div>
    </div>
</x-volt-panel>
