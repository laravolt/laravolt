@extends(config('laravolt.comma.view.layout'))

@section('content')

    <h2 class="ui header">@lang('comma::post.header.edit')</h2>
    <div class="p-y-1">
        {!! SemanticForm::open()->put()->route('comma::posts.update', $post->id) !!}
        {!! SemanticForm::text('title', $post->title)->label(trans('comma::post.attributes.title')) !!}
        {!! SemanticForm::select('category_id', $categories, $post->category_id)->label(trans('comma::post.attributes.category')) !!}
        {!! SemanticForm::textarea('content', $post->content)->label(trans('comma::post.attributes.content'))->id('postContent') !!}
        {!! SemanticForm::selectMultiple('tags[]', $tags, old('tags', $post->tagList))->placeholder('')->label(trans('comma::post.attributes.tags')) !!}
        {!! SemanticForm::submit(trans('comma::post.action.submit'))->addClass('primary') !!}
        {!! SemanticForm::close() !!}
    </div>
@endsection

@push('head')
<link rel="stylesheet" href="{{ asset('lib/redactor/redactor.css') }}">
<style>
    body {
        overflow-x: initial;
    }
</style>
@endpush

@push('body')
<script src="{{ asset('lib/redactor/redactor.min.js') }}"></script>
<script>
    $(function () {
        $('#postContent').redactor({
            minHeight: 500,
            toolbarFixedTopOffset: 60,
            imageUpload: '{{ route('comma::media.store') }}',
            imageResizable: true,
            imagePosition: true,
            imageUploadFields: {
                '_token': '{{ csrf_token() }}',
                'post_id': '{{ $post->id }}'
            }
        });
    });
</script>

@endpush
