<x-volt-app :title="$instance->variables->getValue('full_name')">
    <x-slot name="actions">
        @include('workflow._button-map', ['instance' => $instance])
        <x-volt-link-button
                :url="route('workflow::module.instances.index', $module->id)"
                icon="left arrow"
                :label="__('Back')"/>
    </x-slot>

    <x-volt-panel title="Start">
        {!! form()->make($module->startFormSchema())->bindValues($variables)->display() !!}
    </x-volt-panel>

    @foreach($completedTasks as $completedTask)
        <x-volt-panel :title="$completedTask->name">
            {!! form()->make(config("laravolt.workflow-forms.{$module->id}.{$completedTask->taskDefinitionKey}"))->bindValues($variables)->display() !!}
        </x-volt-panel>
    @endforeach

    @foreach($openTasks as $task)
        <x-volt-panel :title="$task->name">
        {!! form()->put(route('workflow::module.tasks.update', [$module->id, $task->id]))->horizontal() !!}
        {!! form()->hidden('_task_definition_key', $task->taskDefinitionKey) !!}
        {!! form()->make(config("laravolt.workflow-forms.{$module->id}.{$task->taskDefinitionKey}"))->render() !!}
        {!! form()->action(form()->submit(__('Submit'))) !!}
        {!! form()->close() !!}
        </x-volt-panel>
    @endforeach

</x-volt-app>
