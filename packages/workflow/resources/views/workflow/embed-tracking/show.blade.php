<x-volt-public>
    <div style="display: flex; align-items: center; justify-content: center">
        <x-volt-panel title="Tracking" icon="compass" style="min-width:500px;">

            <x-slot name="action">
                <x-volt-workflow-diagram-button :instance="$instanceHistory"></x-volt-workflow-diagram-button>
            </x-slot>

            <div class="ui very relaxed divided list">

                <div class="item">
                    <div class="image" style="color: green">
                        <x-volt-icon name="check-circle"/>
                    </div>
                    <div class="content">
                        <div class="header">Start</div>
                        <div class="description">{{ $instanceHistory->startTime->setTimezone(config('app.timezone'))->isoFormat('LLLL') }}</div>
                    </div>
                </div>

                @foreach($completedTasks as $completedTask)
                    <div class="item">
                        <div class="image" style="color: green">
                            <x-volt-icon name="check-circle"/>
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
                            <div class="description">Sedang diproses...</div>
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
