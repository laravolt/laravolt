<x-laravolt::layout.app :title="$module->name">
    <x-slot name="actions">
        <x-laravolt::link-button
                :url="route('workflow::module.instances.index', $module->id)"
                icon="left arrow"
                :label="__('Back')"/>
    </x-slot>

    {!! form()->open()->horizontal() !!}

    <x-laravolt::panel title="Start">
        {!! form()->make($module->startFormSchema())->bindValues($variables)->display() !!}
    </x-laravolt::panel>

    @foreach($completedTasks as $completedTask)
        <x-laravolt::panel :title="$completedTask->name">
            {!! form()->make(config("laravolt.workflow-forms.{$module->id}.{$completedTask->taskDefinitionKey}"))->bindValues($variables)->display() !!}
        </x-laravolt::panel>
    @endforeach
    {!! form()->close() !!}

</x-laravolt::layout.app>
