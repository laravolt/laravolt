<x-laravolt::layout.app :title="$instance->variables->getValue('full_name')">
    <x-slot name="actions">
        @include('workflow._button-map', ['instance' => $instance])
        <x-laravolt::link-button
                :url="route('workflow::module.instances.index', $module->id)"
                icon="left arrow"
                :label="__('Back')"/>
    </x-slot>

    <x-laravolt::panel title="Start">
        {!! form()->make($module->startFormSchema())->bindValues($variables)->display() !!}
    </x-laravolt::panel>

    @foreach($completedTasks as $completedTask)
        <x-laravolt::panel :title="$completedTask->name">
            {!! form()->make(config("laravolt.workflow-forms.{$module->id}.{$completedTask->taskDefinitionKey}"))->bindValues($variables)->display() !!}
        </x-laravolt::panel>
    @endforeach

    @foreach($openTasks as $task)
        <x-laravolt::panel :title="$task->name">
        {!! form()->put(route('workflow::module.tasks.update', [$module->id, $task->id]))->horizontal() !!}
        {!! form()->hidden('_task_definition_key', $task->taskDefinitionKey) !!}
        {!! form()->make(config("laravolt.workflow-forms.{$module->id}.{$task->taskDefinitionKey}"))->render() !!}
        {!! form()->action(form()->submit(__('Submit'))) !!}
        {!! form()->close() !!}
        </x-laravolt::panel>
    @endforeach

</x-laravolt::layout.app>
