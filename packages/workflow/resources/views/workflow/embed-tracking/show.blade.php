<x-volt-public>
    @section('body.class', 'workflow-tracker')
    <div style="margin-top: 2rem" class="ui two column centered stackable grid">

        <div class="row">
            <div class="column">
                <x-volt-panel :title="$module->name" icon="compass">

                    <x-slot name="action">
                        <x-volt-workflow-diagram-button :instance="$instanceHistory"></x-volt-workflow-diagram-button>
                    </x-slot>

                    <h3 class="ui header" style="font-family: monospace">
                        <div class="sub header">Tracking Code</div>
                        {{ $trackingCode }}
                    </h3>

                    <form class="ui form m-b-4">
                        {!! form()->input('url', url()->current())->prependLabel('URL')->addClass('fluid')->hint('This is secret URL. Do not share with others.') !!}
                    </form>

                    @if(!empty($module->trackerVariables))
                        <div class="ui form m-b-3">
                            {!! form()->make($schema)->bindValues($variables)->display() !!}
                        </div>
                    @endif

                    <div class="ui very relaxed divided list">

                        <div class="item">
                            <div class="image">
                                <x-volt-icon name="rocket"/>
                            </div>
                            <div class="content">
                                <div class="header">Start</div>
                                <div class="description">{{ $instanceHistory->startTime->setTimezone(config('app.timezone'))->isoFormat('LLLL') }}</div>
                            </div>
                        </div>

                        @foreach($completedTasks as $completedTask)
                            <div class="item">
                                <div class="image">
                                    <x-volt-icon name="badge-check"/>
                                </div>
                                <div class="content">
                                    <div class="header">{{ $completedTask->name }}</div>
                                    <div class="description">{{ $completedTask->endTime->setTimezone(config('app.timezone'))->isoFormat('LLLL') }}</div>
                                </div>
                            </div>
                        @endforeach


                        @foreach($ongoingTasks as $task)
                            <div class="item">
                                <div class="image" style="color: darkgrey">
                                    <x-volt-icon name="spinner"/>
                                </div>
                                <div class="content">
                                    <div class="header">{{ $task->name }}</div>
                                    <div class="description">Menunggu diproses...</div>
                                </div>
                            </div>
                        @endforeach


                    </div>

                    <div class="ui message info">
                        Current State: {{ $instanceHistory->state }}
                    </div>

                </x-volt-panel>
            </div>
        </div>

        @foreach($availableTasks as $task)
            <div class="row">
                <div class="column">
                    <x-volt-panel :title="$task->name">
                        {!! form()->put(route('workflow::module.tasks.update', [$module->id, $task->id]))->horizontal() !!}
                        {!! form()->hidden('_task_definition_key', $task->taskDefinitionKey) !!}
                        {!! form()->make($module->formSchema($task->taskDefinitionKey))->render() !!}
                        {!! form()->action(form()->submit(__('Submit'))) !!}
                        {!! form()->close() !!}
                    </x-volt-panel>
                </div>
            </div>
        @endforeach

    </div>
</x-volt-public>
