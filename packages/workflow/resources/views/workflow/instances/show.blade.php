<x-laravolt::layout.app :title="$definition->present_title">
    <x-slot name="actions">
        <x-laravolt::link-button
                :url="route('workflow::definitions.index')"
                icon="left arrow"
                :label="__('Back')"/>
    </x-slot>

    {!! form()->open() !!}
{{--    {!! form()->make(config('workflow-forms.proc_rekrutmen'))->display() !!}--}}
    {!! form()->close() !!}

</x-laravolt::layout.app>
