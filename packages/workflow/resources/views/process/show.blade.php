@extends(
    config('laravolt.workflow.view.layout'),
    [
        '__page' => [
            'title' => $module->label,
            'actions' => [
                view('workflow::components.button-map', ['id' => $processInstance->id])->render(),
                [
                    'label' => __('Kembali ke Index'),
                    'class' => '',
                    'icon' => 'arrow up',
                    'url' => $module->getModel()->getIndexUrl()
                ],
            ]
        ],
    ]
)

@section('content')
    @foreach($completedTasks as $task)
        {!! \Laravolt\Workflow\Presenters\TaskInfo::make($module, $task)->render() !!}
    @endforeach

    @can('edit', $module->getModel())
        @foreach($forms as $form)
            @component('laravolt::components.panel', ['title' => "<div title='{$form->key()}'>{$form->title()}</div>"])
                {!! $form->render() !!}
            @endcomponent
        @endforeach
    @endcan

    @foreach($otherTasks as $task)
        @component('laravolt::components.panel', ['title' => "<div title='{$task['model']->taskDefinitionKey}'>{$task['model']->name}</div>"])
            <div class="ui placeholder basic segment">
                <div class="ui icon header">
                    <i class="user clock icon"></i>
                    Task ini sedang dikerjakan oleh Tim lain.
                </div>
                @include('workflow::components.button-map', ['id' => $processInstance->id, 'label' => 'Lihat Diagram Proses'])
            </div>
        @endcomponent
    @endforeach

@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script>
            @can('edit', $module->getModel())
            @foreach($forms as $form)
        var form = new Vue({
                el: '#{{ $form->key() }}',
                data: @json($form->getBindings()),
                watch: {
        @foreach($form->getFields() as $field)
        @switch($field->field_type)
        @case('dropdown')
        @case('dropdownDB')
        {{ $field->field_name }}:

        function (value) {
            if (value === null) {
                $('select[name="{{ $field->field_name }}"]').dropdown('clear');
            } else {
                $('select[name="{{ $field->field_name }}"]').dropdown('set selected', value);
            }
        }

        ,
        @break
        @endswitch
        @endforeach
        }
        })
        @endforeach
        @endcan

        $(function () {

            // set default date untuk beberapa input date dengan flag khusus
            $('input[type="date"].today').each(function (idx, elm) {
                if (typeof form !== 'undefined') {
                    var field = $(elm).attr('name');
                    if (field in form) {
                        form[field] = moment().format('YYYY-MM-DD');
                    }
                }
            });

            $(document).on('autofill.selected', function (event, payload) {
                $.each(payload, function (key, value) {
                    form[key] = value;
                });
            });

            //Auto score calculation
            $(function(){
                $('[data-score]').on('change', function(e){
                    var score = 0;
                    $('[data-score]:checked').each(function(idx, elm){
                        score += parseInt($(elm).data('score'));
                    });
                    var key = $('[data-role="score"]').attr('name');
                    form[key] = score;
                    $('[name="'+key+'"]').val(score);
                    $('[name="'+key+'"]').trigger('change');
                });
            });
        });
    </script>

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
        window.saving = false;

        $(function () {
            $('.panel').on('click', '.panel-header', function (e) {
                if ($(e.target).is('a') === false) {
                    $(e.delegateTarget).toggleClass('active');
                }
            })

            let $form = $('[data-form-task]');
            let $actionButtons = $form.find('.action .button[type="submit"]');

            $form.on('submit', function(){
                $actionButtons.addClass('loading disabled');
                window.saving = true;
            });

            @if(config('laravolt.workflow.auto_save'))
                let formData = $form.serialize();

                // Capture form initial state several seconds after page loaded
                // to make sure all value are setted
                setTimeout(function () {
                    formData = $form.serialize();
                }, 3000);

                let autoSave = function(){

                    let newFormData = $form.serialize();

                    if (newFormData != formData && !window.saving) {
                        formData = newFormData;

                        let autoSaveToast = $('body').toast({
                            message: 'Menyimpan draf...',
                            position: 'top center',
                            compact: false,
                            class: 'black',
                            displayTime:0,
                            transition: {
                              showDuration:0
                            }
                        });

                        $actionButtons.addClass('loading disabled');
                        window.saving = true;

                      // remove default_method = POST and replace it with PUT
                      let data = $form.serializeArray().filter(function (item) {
                        return item.name != '_method';
                      });
                      data.push({name:"_method", value:'PUT'});

                      $.ajax({
                            url: $form.attr('action') + '/autosave',
                            dataType:'json',
                            method: $form.attr('method'),
                            data: data
                        }).done(function() {

                        }).fail(function() {

                        }).always(function() {
                            window.saving = false;
                            autoSaveToast.toast('close');
                            $actionButtons.removeClass('loading disabled');
                        });
                    }
                };
                setInterval(autoSave, parseInt({{ config('laravolt.workflow.auto_save') }}));
            @endif
        });
    </script>
@endpush
