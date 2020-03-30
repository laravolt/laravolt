<?php
$processHistory = (new \Laravolt\Camunda\Models\ProcessInstanceHistory($id));
$ids = collect($processHistory->tasks())->pluck('taskDefinitionKey');

if ($ids->isEmpty()) {
    $ids = collect($processHistory->activities())
        ->filter(function($item) {
            return $item->activityType == 'noneEndEvent';
        })
        ->pluck('activityId');
}

if ($ids->isEmpty()) {
    $subProcessess = $processHistory->getSubProcess();
    $ids = [];
    foreach ($subProcessess as $process) {
        $ids[] = optional($process->processDefinition())->key;
    }
}

$url = route('workflow::process.xml', $id);
?>

<button class="ui button" camunda-map-button><i class="icon project diagram"></i> {{ $label ?? 'Diagram Proses' }}</button>

@pushonce('body:process-map')
<div class="ui fullscreen overlay modal" camunda-map-modal>
    <i class="close icon"></i>
    <div class="header">
        Process {{ $id ?? '' }}
    </div>
    <div class="content" camunda-map-diagram style="cursor: move">
        <div class="ui active inverted dimmer" camunda-diagram-loader>
            <div class="ui indeterminate elastic text loader">Memuat Diagram Proses</div>
        </div>
    </div>
</div>
@endpushonce

@pushonce('style:process-map')
    <style>
        .highlight:not(.djs-connection) .djs-visual > :nth-child(1) {
            {{--fill: {{ config('laravolt.workflow.diagram.highlight_color') }} !important; /* color elements as green */--}}
        }

        .highlight-overlay {
            background-color: {{ config('laravolt.workflow.diagram.highlight_color') }};
            pointer-events: none; /* no pointer events, allows clicking through onto the element */
            border-radius: 10px;
        }
    </style>
@endpushonce

@pushonce('script:process-map')
    <script src="https://unpkg.com/bpmn-js@5.1.2/dist/bpmn-navigated-viewer.development.js"></script>

    <script>
      $(function () {

        $('[camunda-map-modal]').modal({
          onVisible: function (a,b,c) {
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
              @foreach ($ids as $id)
                shape = elementRegistry.get('{{ $id }}');
            $overlayHtml = $('<div class="highlight-overlay">').css({
              width: shape.width,
              height: shape.height
            });
            overlays.add(
              '{{ $id }}',
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
@endpushonce
