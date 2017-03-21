@extends(config('laravolt.comma.view.layout'))

@section('content')

    <h2 class="ui header">@lang('comma::category.header.create')</h2>
    <div class="p-y-1">
        {!! SemanticForm::open()->route('comma::categories.store') !!}
        {!! SemanticForm::text('name')->label(trans('comma::category.attributes.name')) !!}
        {!! SemanticForm::submit(trans('comma::category.action.submit'))->addClass('primary') !!}
        {!! SemanticForm::close() !!}
    </div>
@endsection
