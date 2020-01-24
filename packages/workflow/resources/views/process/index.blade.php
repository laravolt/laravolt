@extends(
    config('laravolt.workflow.view.layout'),
    [
        '__page' => [
            'title' => $module->label
        ],
    ]
)

@foreach(\Illuminate\Support\Arr::get($module->routes, 'index.reports', []) as $report)
    @push('page.actions')
        {!! form()->get(route('workflow::process.report', [$module->id, $report['format'] ?? 'pdf'])) !!}
        {!! form()->hidden('ids')->data('role', 'ids') !!}
        {!! form()->hidden('path', $report['path']) !!}
        {!! form()->hidden('download', true) !!}
        @foreach(collect(request()->query())->except('page') as $queryString => $value)
            {!! form()->hidden($queryString, $value) !!}
        @endforeach
        {!! form()->submit('<i class="icon print"></i> Cetak <div data-counter-ids class="ui label circular orange mini floating left hidden"></div>')->removeClass('primary') !!}
        {!! form()->close() !!}
    @endpush
@endforeach

@push('page.actions')
    @includeWhen(
        auth()->user()->can('create', $module->getModel()),
        'laravolt::components.button',
        [
            'action' => [
                'url' => route('workflow::process.create', [$module->id]),
                'label' => 'Tambah',
                'icon' => 'plus',
                'class' => 'primary'
            ]
        ]
    )
@endpush

@section('content')
    {!! $table !!}
@endsection

@push('script')
    <script>
        $(function () {
            $('[data-toggle="checkall"]').on('change', function () {
                let ids = $(this).val();
                $('[data-role="ids"]').val(ids);
                let count = JSON.parse(ids).length;
                if (count > 0) {
                    $('[data-counter-ids]').html(count).removeClass('hidden');
                } else {
                    $('[data-counter-ids]').html(count).addClass('hidden');
                }
            })
        });
    </script>
@endpush
