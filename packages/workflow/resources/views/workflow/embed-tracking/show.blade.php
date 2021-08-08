<x-volt-public>
    <div style="display: flex; align-items: center; justify-content: center; margin-top: 2rem">

        <x-volt-panel :title="$module->name" icon="compass" style="min-width:500px;">

            <x-slot name="action">
                <x-volt-workflow-diagram-button :instance="$instanceHistory"></x-volt-workflow-diagram-button>
            </x-slot>

            <form class="ui form m-b-3">
                {!! form()->input('url', url()->current())->prependLabel('URL')->addClass('fluid')->hint('This is secret URL. Do not share with others.') !!}
            </form>

            @if(!empty($module->trackerVariables))
                <div class="ui form m-b-3">
                    {!! form()->make($schema)->bindValues($variables)->display() !!}
                </div>
            @endif

            <h3 class="ui horizontal divider section">Status</h3>

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
</x-volt-public>
