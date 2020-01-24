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

    @component('laravolt::components.panel', ['title' => "<div class='sub header'>Edit</div>".$form->taskName()])
        <div id="startForm">
            {!! $form->render() !!}
        </div>
    @endcomponent

@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script>
      var form = new Vue({
        el: '#startForm',
        data: @json($form->getBindings())
      })
    </script>
@endpush
