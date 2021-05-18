<x-volt-app title="Import BPMN">
    <x-slot name="actions">
        <x-volt-link-button
                :url="route('workflow::definitions.index')"
                icon="left arrow"
                :label="__('Back')"/>
    </x-slot>

    <x-volt-panel>
        <div class="ui middle aligned divided list relaxed">
            @foreach($definitions as $definition)
                <div class="item">
                    <div class="right floated content">
                        {!! form()->post(route('workflow::definitions.store')) !!}
                        {!! form()->hidden('key', $definition->key) !!}
                        <x-volt-button class="small secondary">{{ __('Add') }}</x-volt-button>
                        {!! form()->close() !!}
                    </div>
                    <div class="header">{{ $definition->key }}</div>
                    {{ $definition->name }}
                </div>
            @endforeach
        </div>
    </x-volt-panel>

</x-volt-app>
