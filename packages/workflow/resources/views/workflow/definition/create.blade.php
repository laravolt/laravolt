<x-volt-app title="Import BPMN">
    <x-slot name="actions">
        <x-volt-link-button
                :url="route('workflow::definitions.index')"
                icon="left arrow"
                :label="__('Back')"/>
    </x-slot>

    <x-volt-panel>
        <div class="divide-y divide-gray-200">
            @foreach($definitions as $definition)
                <div class="flex items-center justify-between py-3">
                    <div class="flex-1">
                        <div class="font-medium text-gray-800 dark:text-neutral-200">{{ $definition->key }}</div>
                        <div class="text-sm text-gray-600 dark:text-neutral-400">{{ $definition->name }}</div>
                    </div>
                    <div class="shrink-0">
                        {!! form()->post(route('workflow::definitions.store')) !!}
                        {!! form()->hidden('key', $definition->key) !!}
                        <x-volt-button class="small secondary">{{ __('Add') }}</x-volt-button>
                        {!! form()->close() !!}
                    </div>
                </div>
            @endforeach
        </div>
    </x-volt-panel>

</x-volt-app>
