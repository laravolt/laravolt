@extends(config('laravolt.comma.view.layout'))

@section('content')

    <h2 class="ui header">@lang('comma::category.header.edit')</h2>
    <div class="p-y-1">
        {!! SemanticForm::open()->put()->route('comma::categories.update', $category->id) !!}
        {!! SemanticForm::text('name', $category->name)->label(trans('comma::category.attributes.name')) !!}
        {!! SemanticForm::submit(trans('comma::category.action.submit'))->addClass('primary') !!}
        {!! SemanticForm::close() !!}
    </div>
@endsection
