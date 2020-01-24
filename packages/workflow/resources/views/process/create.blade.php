@extends(
    config('laravolt.workflow.view.layout'),
    [
        '__page' => [
            'title' => $form->processName(),
            'actions' => [
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

    @component('laravolt::components.panel', ['title' => $form->title()])
        <div id="startForm">
            {!! $form->render() !!}
        </div>
    @endcomponent

@endsection

@push('style')
@endpush
@push('script')
    {{--    @TODO: use local assets, not CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script>
      var form = new Vue({
        el: '#startForm',

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
              },

            @break
            @endswitch
            @endforeach
        }
      })

      $(function () {
        // set default date untuk beberapa input date dengan flag khusus
        $('input[type="date"].today').each(function(idx, elm){
            if (typeof form !== 'undefined') {
                var field = $(elm).attr('name');
                if (field in form) {
                    form[field] = moment().format('YYYY-MM-DD');
                }
            }
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

        $(document).on('autofill.selected', function (event, payload) {
          $.each(payload, function (key, value) {
            form[key] = value;
          });
        });
      });
    </script>
@endpush
