<x-volt-app :title="$module->name.': '.$instance->business_key">
    <x-slot name="actions">
        <x-volt-workflow-diagram-button :instance="$instance"></x-volt-workflow-diagram-button>
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
            {!! form()->make($module->formSchema($completedTask->taskDefinitionKey))->bindValues($variables)->display() !!}
        </x-volt-panel>
    @endforeach

    @foreach($openTasks as $task)
        <x-volt-panel :title="$task->name">
        {!! form()->put(route('workflow::module.tasks.update', [$module->id, $task->id]))->horizontal() !!}
        {!! form()->hidden('_task_definition_key', $task->taskDefinitionKey) !!}
        {!! form()->make($module->formSchema($task->taskDefinitionKey))->render() !!}
        {!! form()->action(form()->submit(__('Submit'))) !!}
        {!! form()->close() !!}
        </x-volt-panel>
    @endforeach

</x-volt-app>
