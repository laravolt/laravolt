@include('workflow::components.button-map', ['id' => $processInstance->id])

<div class="ui segments panel {{ $attributes['active'] ? 'active':'' }}">
    <div class="ui secondary segment fitted panel-header" data-task-name="{{ $taskName }}">
        <div class="ui secondary menu">
            <div class="item p-r-0"><i class="icon angle down"></i></div>
            <div class="item p-l-0"><h3>{{ $title }}</h3></div>
            <div class="right menu">
                @if(config('laravolt.workflow.process_instance.editable'))
                    <div class="item">
                        <a href="{{ route('workflow::process.edit', [$module->id, $processInstance->id]) }}"
                           class='ui basic button small'>
                            <i class='icon edit'></i> Edit
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="ui segment fitted panel-body">
        {!! form()->make($formDefinition)->display() !!}
    </div>
</div>

@pushonce('body:panel')

<style>
    .ui.fitted.segment > .ui.table {
        border: 0 none;
    }

    .panel {

    }

    .panel > .panel-header {
        cursor: pointer;
    }

    .panel > .panel-body {
        display: none;
        transition: height 350ms ease-in-out;
    }

    .panel.active > .panel-body {
        display: block;
    }

    .panel > .panel-header .icon.angle.down {
        transition: .5s all;
    }

    .panel.active > .panel-header .icon.angle.down {
        transform: rotate(180deg);
    }
</style>
<script>
  $(function () {
      $('.panel').on('click', '.panel-header', function (e) {
          if ($(e.target).is('a') === false) {
              $(e.delegateTarget).toggleClass('active');
          }
      })
  });
</script>
@endpushonce
