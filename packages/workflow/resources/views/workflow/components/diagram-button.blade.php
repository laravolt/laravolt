<?php

$taskNames = [];
$tasks = \Laravolt\Camunda\Http\TaskClient::getByProcessInstanceId($instance->id);
foreach ($tasks as $task) {
    $taskNames[] = $task->taskDefinitionKey;
}
$url = route('workflow::definitions.xml', $instance->definition_id);
?>

<button class="ui secondary button {{ $class ?? '' }} {{ config('laravolt.ui.color') }}" camunda-map-button>
    <i class="icon project diagram"></i> {{ $label ?? 'Flow Diagram' }}
</button>

@once
    @push('body')
        <div class="ui fullscreen overlay modal" camunda-map-modal>
            <i class="close icon"></i>
            <div class="header">
                Process {{ $id ?? '' }}
            </div>
            <div class="content" camunda-map-diagram style="cursor: move">
                <div class="ui active inverted dimmer" camunda-diagram-loader>
                    <div class="ui indeterminate elastic text loader"></div>
                </div>
            </div>
        </div>
        <style>
            .highlight:not(.djs-connection) .djs-visual > :nth-child(1) {
                fill: {{ config('laravolt.ui.colors.'.config('laravolt.ui.color')) }} !important; /* color elements as green */
            }

            .highlight-overlay {
                background-color: {{ config('laravolt.ui.colors.'.config('laravolt.ui.color')) }}; /* color elements as green */
                opacity: 0.4;
                pointer-events: none; /* no pointer events, allows clicking through onto the element */
                border-radius: 10px;
            }
        </style>
        <script src="https://unpkg.com/bpmn-js@8.3.1/dist/bpmn-viewer.production.min.js"></script>

        <script>
            $(function () {

                $('[camunda-map-modal]').modal({
                    onVisible: function (a, b, c) {
                        $('[camunda-map-diagram]').height('100%');

                        // load + show diagram
                        $.get("{{ $url }}", showDiagram, 'text');
                    }
                });

                // Show the modal
                $('[camunda-map-modal]').modal('attach events', '[camunda-map-button]', 'show');

                // Render diagram
                var viewer = new BpmnJS({
                    container: '[camunda-map-diagram]'
                });
                var canvas = viewer.get('canvas');
                var zoomLevel = 'fit-viewport';

                function showDiagram(diagramXML) {
                    viewer.importXML(diagramXML, function () {
                        var overlays = viewer.get('overlays');
                        var elementRegistry = viewer.get('elementRegistry');

                        canvas.zoom(zoomLevel);

                        // Option 1: Color via Overlay
                        var shap = "";
                        var $overlayHtml = "";
                        @foreach ($taskNames as $task)
                            shape = elementRegistry.get('{{$task}}');
                        $overlayHtml = $('<div class="highlight-overlay">').css({
                            width: shape.width,
                            height: shape.height
                        });
                        overlays.add(
                            '{{$task}}',
                            {
                                position: {
                                    top: 0,
                                    left: 0
                                },
                                html: $overlayHtml
                            });
                        @endforeach
                    });

                    $('[camunda-diagram-loader]').removeClass('active');
                }

                $('[camunda-map-diagram]').on('click', function () {
                    if (zoomLevel == 1) {
                        zoomLevel = 'fit-viewport';
                    } else {
                        zoomLevel = 1;
                    }
                    canvas.zoom(zoomLevel);
                });

            });
        </script>
    @endpush
@endonce
