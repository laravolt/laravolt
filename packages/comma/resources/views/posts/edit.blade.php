@extends(config('laravolt.comma.view.layout'))

@section('content')

    <h2 class="ui header">@lang('comma::post.header.edit')</h2>
    <div class="p-y-1">
        {!! SemanticForm::open()->put()->route('comma::posts.update', $post->id) !!}
        {!! SemanticForm::text('title', $post->title)->label(trans('comma::post.attributes.title')) !!}
        {!! SemanticForm::select('category_id', $categories, $post->category_id)->label(trans('comma::post.attributes.category')) !!}
        {!! SemanticForm::textarea('content', $post->content)->label(trans('comma::post.attributes.content')) !!}
        {!! SemanticForm::selectMultiple('tags[]', $tags, old('tags', $post->tagList))->placeholder('')->label(trans('comma::post.attributes.tags')) !!}
        {!! SemanticForm::submit(trans('comma::post.action.submit'))->addClass('primary') !!}
        {!! SemanticForm::close() !!}
    </div>
@endsection
