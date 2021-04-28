<x-laravolt::layout.app title="Import BPMN">
    <x-slot name="actions">
        <x-laravolt::link-button
                :url="route('workflow::definitions.index')"
                icon="left arrow"
                :label="__('Back')"/>
    </x-slot>

    <x-laravolt::panel>
        <div class="ui middle aligned divided list relaxed">
            @foreach($definitions as $definition)
                <div class="item">
                    <div class="right floated content">
                        {!! form()->post(route('workflow::definitions.store')) !!}
                        {!! form()->hidden('id', $definition->id) !!}
                        <x-laravolt::button class="small secondary">{{ __('Add') }}</x-laravolt::button>
                        {!! form()->close() !!}
                    </div>
                    <div class="header">{{ $definition->key }}</div>
                    {{ $definition->name }}
                </div>
            @endforeach
        </div>
    </x-laravolt::panel>

</x-laravolt::layout.app>
