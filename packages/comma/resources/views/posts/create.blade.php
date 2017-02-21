@extends(config('laravolt.comma.view.layout'))

@section('content')

    <h2 class="ui header">@lang('comma::post.header.create')</h2>
    <div class="p-y-1">
        {!! SemanticForm::open()->route('comma::posts.store') !!}
        {!! SemanticForm::text('title')->label(trans('comma::post.attributes.title')) !!}
        {!! SemanticForm::select('category_id', $categories)->label(trans('comma::post.attributes.category')) !!}
        {!! SemanticForm::textarea('content')->label(trans('comma::post.attributes.content')) !!}
        {!! SemanticForm::selectMultiple('tags[]', $tags)->placeholder('')->label(trans('comma::post.attributes.tags')) !!}
        {!! SemanticForm::submit(trans('comma::post.action.submit'))->addClass('primary') !!}
        {!! SemanticForm::close() !!}
    </div>
@endsection
