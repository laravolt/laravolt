@extends(
    config('laravolt.workflow.view.layout'),
    [
        '__page' => [
            'title' => $form->processName(),
            'actions' => []
        ],
    ]
)

@section('content')

    @component('laravolt::components.panel', ['title' => "<div class='sub header'>Edit</div><div title='{$form->key()}'>{$form->title()}</div>"])
        {!! $form->render() !!}
    @endcomponent

@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script>

      $('form .field').each(function (idx, elm) {
        var name = $(elm).find(':input').attr('name');
        $(elm).find('label').attr('title', name);
      });

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

    $(function(){
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
@endpush
