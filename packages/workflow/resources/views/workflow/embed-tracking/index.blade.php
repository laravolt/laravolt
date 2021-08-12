<x-volt-public>
    @section('body.class', 'workflow-tracker')
    <div style="display: flex; align-items: center; justify-content: center; margin-top: 2rem;">
        <x-volt-panel :title="$module->name" icon="compass" style="min-width: 500px">
            {!! form()->get() !!}
            {!! form()->input('code', request('code'))->label('Tracking Code') !!}
            {!! form()->submit('Check')->addClass('fluid') !!}
            {!! form()->close() !!}

            @if($trackingCode)
                @if($instanceHistory)
                    <div class="ui message success">
                        Valid Code.
                        Check
                        <a href="{{ route('workflow::tracker.show', [$module->id, $trackingCode]) }}">
                            <strong style="text-decoration: underline">this link</strong>
                        </a>
                        to see more detailed information.
                    </div>
                @else
                    <div class="ui message warning">Invalid Code</div>
                @endif
            @endif

        </x-volt-panel>

    </div>

</x-volt-public>
